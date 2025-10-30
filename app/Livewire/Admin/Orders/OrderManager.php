<?php

namespace App\Livewire\Admin\Orders;

use App\Livewire\Traits\WithSorting;
use App\Models\Notification;
use App\Models\Order;
use App\Services\ProductService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Order Management')]
class OrderManager extends Component
{
    use WithPagination, WithSorting;

    protected ProductService $productService;

    public $search = '';

    public $statusFilter = '';

    public $dateFrom;

    public $dateTo;

    public $perPage = 10;

    public $orderId;

    public $orderStatus;

    protected $listeners = [
        'orderUpdated' => '$refresh',
    ];

    public function boot(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openStatusModal($orderId)
    {
        $this->orderId = $orderId;

        if ($orderId) {
            $order = Order::findOrFail($orderId);
            $this->orderStatus = $order->status;
        }

        Flux::modal('update-order-status')->show();
    }

    public function closeModal()
    {
        $this->resetForm();
        Flux::modals()->close();
    }

    public function updateOrderStatus()
    {
        $this->validate([
            'orderStatus' => ['required', Rule::in(Order::STATUSES)],
        ]);

        $disallowCancelIf = ['confirmed', 'processing', 'shipped', 'delivered'];
        $disallowPendingIf = ['confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

        $order = Order::query()
            ->with(['items.product', 'items.warehouse'])
            ->findOrFail($this->orderId);

        // Business rules enforcement
        if (in_array($order->status, $disallowCancelIf) && $this->orderStatus === 'cancelled') {
            $this->dispatch(
                'notify',
                variant: 'danger',
                title: 'Action Not Allowed',
                message: 'Cannot cancel an order that is already confirmed, processing, shipped, or delivered.',
            );
            $this->closeModal();

            return;
        }

        if (in_array($order->status, $disallowPendingIf) && $this->orderStatus === 'pending') {
            $this->dispatch(
                'notify',
                variant: 'danger',
                title: 'Action Not Allowed',
                message: 'Cannot revert an order to pending status once it is confirmed, processing, shipped, delivered, or cancelled.',
            );
            $this->closeModal();

            return;
        }

        // Check if order status is already set to the selected value
        if ($order->status === $this->orderStatus) {
            $this->dispatch(
                'notify',
                variant: 'info',
                title: 'No Changes Made',
                message: 'The order status is already set to the selected value.',
            );
            $this->closeModal();

            return;
        }

        // Update order status
        $order->update(['status' => $this->orderStatus]);

        // Handle stock adjustments and notifications based on status changes
        switch ($this->orderStatus) {
            case 'confirmed':
                // TODO: Send order confirmation email
                // Mail::to($order->client->contact_email)->send(new OrderConfirmed($order));

                foreach ($order->items as $item) {
                    // Reduce stock levels at order confirmation
                    $this->productService->confirmStock($item->productVariant, $item->warehouse_id, $item->quantity, $order->order_number);
                }

                Notification::create([
                    'user_id' => Auth::id(),
                    'client_id' => $order->client_id,
                    'type' => 'success',
                    'title' => 'Order Confirmed',
                    'message' => "Order #{$order->order_number} has been confirmed.",
                    'url' => route('client.order-details', $order->id),
                ]);
                break;
            case 'cancelled':
                foreach ($order->items as $item) {
                    // Return stock levels
                    $this->productService->releaseReservedStock($item->productVariant, $item->warehouse_id, $item->quantity);
                }

                // Update cancelled_at field
                // $order->update(['cancelled_at' => now()]);

                Notification::create([
                    'user_id' => Auth::id(),
                    'client_id' => $order->client_id,
                    'type' => 'success',
                    'title' => 'Order Cancelled',
                    'message' => "Order #{$order->order_number} has been cancelled.",
                    'url' => route('client.order-details', $order->id),
                ]);
                break;
        }
        // if ($this->orderStatus === 'confirmed') {
        //   // TODO: Send order confirmation email
        //   // Mail::to($order->client->contact_email)->send(new OrderConfirmed($order));

        //   foreach ($order->items as $item) {
        //     // Reduce stock levels at order confirmation
        //     $this->productService->confirmStock($item->productVariant, $item->warehouse_id, $item->quantity, $order->order_number);
        //   }

        //   Notification::create([
        //     'user_id' => Auth::id(),
        //     'client_id' => $order->client_id,
        //     'type' => 'success',
        //     'title' => 'Order Confirmed',
        //     'message' => "Order #{$order->order_number} has been confirmed.",
        //     'url' => route('client.order-details', $order->id),
        //   ]);
        // } elseif ($this->orderStatus === 'cancelled') {
        //   foreach ($order->items as $item) {
        //     // Return stock levels
        //     $this->productService->releaseReservedStock($item->productVariant, $item->warehouse_id, $item->quantity);
        //   }

        //   // Update cancelled_at field
        //   $order->update(['cancelled_at' => now()]);

        //   Notification::create([
        //     'user_id' => Auth::id(),
        //     'client_id' => $order->client_id,
        //     'type' => 'success',
        //     'title' => 'Order Cancelled',
        //     'message' => "Order #{$order->order_number} has been cancelled.",
        //     'url' => route('client.order-details', $order->id),
        //   ]);
        // }

        $this->dispatch('orderUpdated');
        $this->dispatch(
            'notify',
            variant: 'success',
            title: 'Order Status Updated',
            message: 'Order status updated successfully.',
        );

        $this->closeModal();
    }

    public function getOrdersProperty()
    {
        $query = Order::with(['client', 'items.product']);

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($this->perPage);
    }

    public function getOrderStatsProperty()
    {
        $stats = [
            'total' => Order::query()->count(),
            'pending' => Order::query()->where('status', 'pending')->count(),
            'processing' => Order::query()->where('status', 'processing')->count(),
            'shipped' => Order::query()->where('status', 'shipped')->count(),
            'delivered' => Order::query()->where('status', 'delivered')->count(),
            'cancelled' => Order::query()->where('status', 'cancelled')->count(),
        ];

        $stats['total_revenue'] = Order::query()->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('total_amount');

        return $stats;
    }

    public function getStatusBadgeColor($status)
    {
        return match ($status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'indigo',
            'shipped' => 'purple',
            'delivered' => 'green',
            'completed' => 'teal',
            'canceled' => 'red',
            default => 'zinc',
        };
    }

    public function getCurrentOrder()
    {
        if ($this->orderId) {
            return Order::find($this->orderId);
        }

        return null;
    }

    private function resetForm()
    {
        $this->reset(['orderId', 'orderStatus']);
    }

    public function render()
    {
        return view('livewire.admin.orders.order-manager', [
            'orders' => $this->orders,
            'orderStats' => $this->orderStats,
        ]);
    }
}
