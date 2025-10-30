<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.shop')]
#[Title('Order Details')]
class OrderDetails extends Component
{
    public $order;

    public function mount($id)
    {
        $this->order = Order::with(['items.product.primaryImage', 'items.product.category', 'payments'])
            ->where('client_id', Auth::user()->client->id)
            ->findOrFail($id);
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

    public function markAsReceived()
    {
        if (in_array($this->order->status, ['delivered'])) {
            $this->order->status = 'completed';
            $this->order->save();

            $this->dispatch(
                'notify',
                variant: 'success',
                title: 'Success',
                message: 'Thank you for confirming receipt of your order.',
            );
        } else {
            $this->dispatch(
                'notify',
                variant: 'error',
                title: 'Error',
                message: 'Order cannot be marked as received in its current status.',
            );
        }
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

    public function render()
    {
        return view('livewire.customer.order-details', [
            'orderSummary' => $this->orderSummary,
            'statusBadgeColor' => $this->getStatusBadgeColor($this->order->status),
        ]);
    }
}
