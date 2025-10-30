<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Services\ProductService;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Order Details')]
class OrderDetails extends Component
{
    protected ProductService $productService;

    public $order;

    public $orderStatus;

    public function boot(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function mount(Order $order)
    {
        $this->order = $order->load([
            'client',
            'items.product.category',
            'items.product.brand',
            'payments',
        ]);

        $this->orderStatus = $this->order->status;
    }

    public function updateOrderStatus()
    {
        $this->validate([
            'orderStatus' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        $disallowCancelIf = ['confirmed', 'processing', 'shipped', 'delivered'];
        $disallowPendingIf = ['confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

        // Business rules enforcement
        if (in_array($this->order->status, $disallowCancelIf) && $this->orderStatus === 'cancelled') {
            $this->dispatch(
                'notify',
                variant: 'danger',
                title: 'Action Not Allowed',
                message: 'Cannot cancel an order that is already confirmed, processing, shipped, or delivered.',
            );
            Flux::modal('update-order-status')->close();

            return;
        }

        if (in_array($this->order->status, $disallowPendingIf) && $this->orderStatus === 'pending') {
            $this->dispatch(
                'notify',
                variant: 'danger',
                title: 'Action Not Allowed',
                message: 'Cannot revert an order to pending status once it is confirmed, processing, shipped, delivered, or cancelled.',
            );
            Flux::modal('update-order-status')->close();

            return;
        }

        // Check if order status is already set to the selected value
        if ($this->order->status === $this->orderStatus) {
            $this->dispatch(
                'notify',
                variant: 'info',
                title: 'No Changes Made',
                message: 'The order status is already set to the selected value.',
            );
            Flux::modal('update-order-status')->close();

            return;
        }

        $this->order->update(['status' => $this->orderStatus]);

        if ($this->orderStatus === 'confirmed') {
            // TODO: Send order confirmation email
            // Mail::to($order->client->contact_email)->send(new OrderConfirmed($order));

            foreach ($this->order->items as $item) {
                // Reduce stock levels
                $this->productService->confirmStock($item->productVariant, $item->warehouse_id, $item->quantity, $this->order->order_number);
            }
        } elseif ($this->orderStatus === 'cancelled') {
            foreach ($this->order->items as $item) {
                // Return stock levels
                $this->productService->releaseReservedStock($item->productVariant, $item->warehouse_id, $item->quantity);
            }
        }

        $this->reset('orderStatus');

        $this->dispatch(
            'notify',
            variant: 'success',
            title: 'Order Status Updated',
            message: 'Order status updated successfully.',
        );

        Flux::modal('update-order-status')->close();
    }

    public function getStatusBadgeColor($status)
    {
        return match ($status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'indigo',
            'shipped' => 'purple',
            'delivered' => 'green',
            'canceled' => 'red',
            default => 'zinc',
        };
    }

    public function getOrderSummaryProperty()
    {
        return [
            'subtotal' => $this->order->items->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            }),
            'tax_amount' => $this->order->tax_amount ?? 0,
            'shipping_amount' => $this->order->shipping_amount ?? 0,
            'total_amount' => $this->order->total_amount,
            'total_items' => $this->order->items->sum('quantity'),
        ];
    }

    public function render()
    {
        return view('livewire.admin.orders.order-details', [
            'orderSummary' => $this->orderSummary,
        ]);
    }
}
