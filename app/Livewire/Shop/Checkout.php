<?php

namespace App\Livewire\Shop;

use App\Models\InventoryLevel;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RestockHistory;
use App\Models\Warehouse;
use App\Services\PaymentService;
use App\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.shop')]
#[Title('Checkout')]
class Checkout extends Component
{
    use WithFileUploads;

    public $orderPlaced = false;

    public $shippingFee = 0; // Flat rate shipping fee for simplicity

    public $orderNumber = '';

    public $paymentMethod = 'cod'; // cod, card, gcash

    public $paymentProof; // For methods like GCash

    public $companyName = '';

    public $contactName = '';

    public $contactEmail = '';

    public $contactPhone = '';

    public $billingAddress = '';

    public $orderNotes = '';

    protected $listeners = [
        'orderPlaced' => '$refresh',
    ];

    public function mount()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $client = $this->client;

        $this->companyName = $client->company_name;
        $this->contactName = $client->contact_name;
        $this->contactEmail = $client->contact_email;
        $this->contactPhone = $client->contact_phone;
        $this->billingAddress = $client->billing_address;
    }

    public function placeOrder()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $cartItems = $user->cartItems;

        if ($cartItems->isEmpty()) {
            Session::flash('error', 'Your cart is empty.');

            return;
        }

        // Check if user has a client relationship
        if (! $this->client) {
            $this->dispatch(
                'notify',
                variant: 'error',
                title: 'Error',
                message: 'Client information is required to place an order. Please contact support.'
            );

            return;
        }

        $this->validate([
            'companyName' => 'required',
            'contactName' => 'required',
            'contactEmail' => 'required|email',
            'contactPhone' => 'required',
            'billingAddress' => 'required',
            'paymentMethod' => 'required',
        ]);

        // Generate a unique order number
        $orderNumber = $this->generateOrderNumber();

        // dd($cartItems);

        try {
            $result = DB::transaction(function () use ($user, $cartItems, $orderNumber) {

                $order = Order::create([
                    'client_id' => $this->client->id,
                    'order_number' => $orderNumber,
                    'status' => 'pending',
                    'total_amount' => $this->grandTotal,
                    'shipping_amount' => $this->shippingFee,
                    'billing_address' => $this->billingAddress,
                    'shipping_address' => $this->billingAddress,
                    'notes' => $this->orderNotes ?? null,
                    'placed_at' => Carbon::now(),
                ]);

                foreach ($cartItems as $item) {
                    $product = Product::find($item->product_id);
                    $productVariant = ProductVariant::find($item->product_variant_id);

                    if (! $product) {
                        $this->dispatch(
                            'notify',
                            variant: 'error',
                            title: 'Error',
                            message: 'There was an error product.'
                        );
                        throw new \Exception("Product ID {$item->product_id} not found.");
                    }

                    $variant = null;
                    if ($item->product_variant_id) {
                        $variant = $product->variants()->find($item->product_variant_id);

                        if (! $variant) {
                            $this->dispatch(
                                'notify',
                                variant: 'error',
                                title: 'Error',
                                message: "Product variant ID {$item->product_variant_id} not found."
                            );
                            throw new \Exception("Product variant ID {$item->product_variant_id} not found.");
                        }

                        // Check variant stock
                        if ($variant->cached_stock < $item->quantity) {
                            $this->dispatch(
                                'notify',
                                variant: 'error',
                                title: 'Error',
                                message: "Insufficient stock for variant {$variant->variant_name}"
                            );
                            throw new \Exception("Insufficient stock for variant {$variant->variant_name}");
                        }
                    }

                    // Check inventory levels and allocate stock from warehouses
                    $warehouse = $this->findWarehouseWithStock($item->product_variant_id, $item->quantity);
                    if (! $warehouse) {
                        throw new \Exception("Insufficient stock for product ID {$item->name}");
                    }

                    // Prepare variant details for order history
                    $variantDetails = null;
                    if ($variant) {
                        $variantDetails = [
                            'variant_name' => $variant->variant_name,
                            'size' => $variant->size,
                            'color' => $variant->color,
                            'model' => $variant->model,
                            'variant_sku' => $variant->variant_sku,
                            'specifications' => $variant->specifications,
                        ];
                    }

                    $order->items()->create([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'variant_details' => $variantDetails,
                        'warehouse_id' => $warehouse->id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->price,
                        'line_total' => $item->quantity * $item->price,
                    ]);

                    // Reserve stock in the warehouse
                    app(ProductService::class)->reserveStock($productVariant, $warehouse->id, $item->quantity);
                }

                // Create payment record
                app(PaymentService::class)->createPayment(
                    $order,
                    $this->grandTotal,
                    $this->paymentMethod,
                    $this->paymentProof
                );

                // Clear the user's cart
                $user->cartItems()->delete();

                return [
                    'order' => $order,
                    'orderNumber' => $orderNumber,
                ];
            });

            $this->orderNumber = $result['orderNumber'];
            $this->orderPlaced = true;

            Notification::create([
                'user_id' => $user->id,
                'type' => 'success',
                'title' => 'Order Placed',
                'message' => "New order #{$this->orderNumber} has been placed.",
                'url' => route('admin.order-details', $result['order']->id),
            ]);

            $this->dispatch('orderPlaced');
            $this->dispatch('refreshNotifications');

            Session::flash('success', "Order placed successfully! Your order number is {$orderNumber}.");
        } catch (\Exception $e) {
            // Session::flash('error', 'There was an error processing your order. Please try again.');
            $this->dispatch(
                'notify',
                variant: 'error',
                title: 'Error',
                message: 'There was an error processing your order. Please try again.'
            );

            return;
        }
    }

    private function generateOrderNumber()
    {
        $prefix = 'BT';
        $date = Carbon::now()->format('Ymd');
        $sequence = str_pad(Order::query()->whereDate('created_at', Carbon::today())->count() + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$sequence}";
    }

    private function findWarehouseWithStock($productVariantId, $quantity)
    {
        // Implement logic to find a warehouse with sufficient stock for the product
        return InventoryLevel::query()
            ->where('product_variant_id', $productVariantId)
            ->whereRaw('(quantity - reserved_quantity) >= ?', [$quantity])
            ->with('warehouse')
            ->first()?->warehouse;
    }

    private function adjustInventoryLevels(ProductVariant $productVariant, Warehouse $warehouse, int $quantity, Order $order)
    {
        $inventoryLevel = InventoryLevel::where('product_variant_id', $productVariant->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        if (! $inventoryLevel || $inventoryLevel->quantity < $quantity) {
            throw new \Exception("Insufficient stock to adjust for product {$productVariant->name} in warehouse {$warehouse->name}");
        }

        $quantityBefore = $inventoryLevel->quantity;
        $quantityAfter = $quantityBefore - $quantity;

        if ($quantityBefore > $quantityAfter) {
            $inventoryLevel->update([
                'quantity' => $quantityAfter,
            ]);
        }

        RestockHistory::create([
            'product_id' => $productVariant->product_id,
            'warehouse_id' => $warehouse->id,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'quantity_change' => $quantity,
            'reason' => 'order_fulfillment',
            'reference_type' => 'order',
            'reference_id' => $order->id,
            'performed_by' => Auth::id(),
        ]);

        app(ProductService::class)->updateCachedStock($productVariant);
    }

    public function getCartTotalProperty()
    {
        return Auth::check() ? Auth::user()->cartItems->sum(fn ($item) => $item->quantity * $item->price) : 0;
    }

    public function getGrandTotalProperty()
    {
        return $this->cartTotal + $this->shippingFee;
    }

    public function getClientProperty()
    {
        if (! Auth::check()) {
            return null;
        }

        return Auth::user()->client;
    }

    public function render()
    {
        return view('livewire.shop.checkout', [
            'cartItems' => Auth::check() ? Auth::user()->cartItems : collect(),
            'cartTotal' => $this->cartTotal,
            'grandTotal' => $this->grandTotal,
        ]);
    }
}
