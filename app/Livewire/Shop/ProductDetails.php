<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.shop')]
#[Title('Product Details')]
class ProductDetails extends Component
{
    public $product;

    public $quantity = 1;

    public $selectedVariantId = null;

    public $selectedVariant = null;

    public $viewMode = 'grid'; // Default view mode

    public string $imageSrc = '';

    protected $listeners = [
        'refreshProduct' => '$refresh',
        'cartUpdated' => '$refresh',
        'productAddedToCart' => '$refresh', // Listen for product added event
    ];

    public function mount($id)
    {
        $this->product = Product::with(['brand', 'category', 'inventoryLevels.warehouse', 'variants' => function ($query) {
            $query->active()->orderBy('variant_name');
        }])->findOrFail($id);

        // If there are variants, select the first one by default
        if ($this->activeVariants->count() > 0) {
            $this->selectedVariantId = $this->activeVariants->first()->id;
            $this->selectVariant($this->selectedVariantId);
        }
    }

    /**
     * Get only active variants for this product
     */
    public function getActiveVariantsProperty()
    {
        return $this->product->variants()->active()->orderBy('variant_name')->get();
    }

    public function selectVariant($variantId)
    {
        $this->selectedVariantId = $variantId;
        $this->selectedVariant = $this->activeVariants->find($variantId);
        $this->quantity = 1; // Reset quantity when variant changes
    }

    public function addToCart()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // Determine available stock and price based on variant or product
        $availableStock = $this->getCurrentStock();
        $price = $this->getCurrentPrice();
        $productName = $this->product->name;
        $displayName = $this->selectedVariant ? $productName.' - '.$this->selectedVariant->getDisplayName() : $productName;

        $this->validate([
            'quantity' => "required|integer|min:1|max:{$availableStock}",
        ]);

        if (! $this->product) {
            $this->dispatch(
                'notify',
                variant: 'error',
                title: 'Error',
                message: 'Product not found.'
            );

            return;
        }

        $user = Auth::user();
        $cartItem = $user->cartItems()->firstOrNew([
            'product_id' => $this->product->id,
            'product_variant_id' => $this->selectedVariantId,
        ]);

        $cartItem->fill([
            'name' => $displayName,
            'sku' => $this->selectedVariant ? $this->selectedVariant->variant_sku : $this->product->sku,
            'price' => $price,
            'image' => $this->product->primaryImage?->url ?? null,
            'available_stock' => $availableStock,
        ]);

        $cartItem->quantity += $this->quantity;

        if ($cartItem->quantity > $availableStock) {
            $this->dispatch(
                'notify',
                variant: 'error',
                title: 'Error',
                message: "Cannot add {$this->quantity} units. Only {$availableStock} units available in stock."
            );

            return;
        }

        $cartItem->save();

        $this->dispatch('productAddedToCart');
        $this->dispatch(
            'notify',
            variant: 'success',
            title: 'Added to Cart',
            message: "Added {$this->quantity} units of {$displayName} to the cart."
        );
    }

    public function getCurrentPrice()
    {
        return $this->selectedVariant ? $this->selectedVariant->getFinalPrice() : $this->product->unit_price;
    }

    public function getCurrentStock()
    {
        return $this->selectedVariant ? $this->selectedVariant->cached_stock : 0;
    }

    public function updateCartQuantity($cartItemId, $itemQuantity)
    {
        $this->validate([
            'itemQuantity' => 'required|integer|min:1',
        ]);

        $cartItem = Auth::user()->cartItems()->find($cartItemId);

        if (! $cartItem) {
            $this->dispatch(
                'notify',
                variant: 'error',
                title: 'Error',
                message: 'Cart item not found.'
            );

            return;
        }

        $itemQuantity = max(0, intval($itemQuantity));
        if ($itemQuantity === 0) {
            $cartItem->delete();
        } else {
            if ($itemQuantity > $cartItem->available_stock) {
                $this->dispatch(
                    'notify',
                    variant: 'error',
                    title: 'Error',
                    message: "Cannot set quantity to {$itemQuantity}. Only {$cartItem->available_stock} units available in stock."
                );

                return;
            }

            $cartItem->quantity = $itemQuantity;
            $cartItem->save();
        }

        $this->dispatch('cartUpdated');
    }

    public function getCartItemCountProperty()
    {
        return Auth::check() ? Auth::user()->cartItems()->sum('quantity') : 0;
    }

    public function getCartTotalProperty()
    {
        return Auth::check() ? Auth::user()->cartItems->sum(fn ($item) => $item->price * $item->quantity) : 0;
    }

    public function openZoom($imageSrc)
    {
        $this->imageSrc = $imageSrc;
        $this->dispatch('openModal', 'image-zoom', ['image' => $this->imageSrc]);

        Flux::modal('image-zoom')->show();
    }

    public function render()
    {
        return view('livewire.shop.product-details', [
            'cartItems' => Auth::check() ? Auth::user()->cartItems : collect(),
            'cartItemCount' => $this->cartItemCount,
            'cartTotal' => $this->cartTotal,
        ]);
    }
}
