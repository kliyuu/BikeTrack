<?php

namespace App\Livewire\Customer;

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.shop')]
#[Title('Return Orders')]
class ReturnRequests extends Component
{
    use WithPagination;

    public ?int $orderId = null;

    public ?int $orderItemId = null;

    public int $quantity = 1;

    public string $reason = '';

    public function getOrdersProperty()
    {
        return Order::query()
            ->where('client_id', Auth::user()->client->id)
            ->whereIn('status', ['delivered'])
            ->with(['items.product'])
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getReturnRequestsProperty()
    {
        return ReturnItem::query()
            ->whereHas('orderItem.order', function ($query) {
                $query->where('client_id', Auth::user()->client->id);
            })
            ->with(['orderItem.product', 'orderItem.order', 'processedBy'])
            ->latest()
            ->get();
    }

    public function submit(): void
    {
        $this->validate([
            'orderItemId' => ['required', Rule::exists('order_items', 'id')],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $orderItem = OrderItem::with('order')->findOrFail($this->orderItemId);

        if ($orderItem->order->client_id !== Auth::user()->client->id) {
            abort(403);
        }

        // Do not exceed purchased quantity
        $alreadyRequested = $orderItem->returns()->sum('quantity');
        $available = max(0, $orderItem->quantity - $alreadyRequested);

        if ($this->quantity > $available) {
            $this->addError('quantity', 'Quantity exceeds available for return.');

            return;
        }

        ReturnItem::create([
            'order_item_id' => $orderItem->id,
            'quantity' => $this->quantity,
            'reason' => $this->reason,
            'status' => 'requested',
            'requested_at' => now(),
            'requested_by' => Auth::id(),
        ]);

        // Notify admin/staff
        Notification::create([
            'user_id' => Auth::id(),
            'type' => 'info',
            'title' => 'New Return Request',
            'message' => "A new return request has been submitted for Order #{$orderItem->order->order_number}.",
            'url' => route('admin.returns'),
        ]);

        $this->reset(['orderItemId', 'quantity', 'reason']);

        $this->dispatch('refreshNotifications');
        $this->dispatch(
            'notify',
            variant: 'success',
            title: 'Success',
            message: 'Return request submitted.'
        );
    }

    public function getStatusBadgeColor(string $status): string
    {
        return match ($status) {
            'requested' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'received' => 'blue',
            default => 'zinc',
        };
    }

    public function render()
    {
        return view('livewire.customer.return-requests', [
            'orders' => $this->orders,
            'returnRequests' => $this->returnRequests,
        ]);
    }
}
