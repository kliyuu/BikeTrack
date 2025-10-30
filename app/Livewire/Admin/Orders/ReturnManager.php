<?php

namespace App\Livewire\Admin\Orders;

use App\Models\InventoryLevel;
use App\Models\Notification;
use App\Models\RestockHistory;
use App\Models\ReturnItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Return Manager')]
class ReturnManager extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $perPage = 10;

    public $returnItemsId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function approve(int $returnId): void
    {
        $return = ReturnItem::with('orderItem.product', 'orderItem.warehouse')->findOrFail($returnId);
        $return->update([
            'status' => 'approved',
            'processed_by' => Auth::id(),
        ]);

        $this->dispatch(
            'notify',
            variant: 'success',
            title: 'Success',
            message: 'Return request has been approved.'
        );
    }

    public function reject(int $returnId): void
    {
        $return = ReturnItem::findOrFail($returnId);
        $return->update([
            'status' => 'rejected',
            'processed_by' => Auth::id(),
        ]);

        $this->dispatch(
            'notify',
            variant: 'success',
            title: 'Success',
            message: 'Return request has been rejected.'
        );
    }

    public function receive(int $returnId): void
    {
        $return = ReturnItem::with('orderItem.order', 'orderItem.product', 'orderItem.warehouse')->findOrFail($returnId);
        if ($return->status !== 'approved') {
            session()->flash('error', 'Only approved returns can be received.');

            return;
        }

        try {
            DB::transaction(function () use ($return) {
                // Restock inventory to the original warehouse
                $inventory = InventoryLevel::firstOrCreate([
                    'product_id' => $return->orderItem->product_id,
                    'warehouse_id' => $return->orderItem->warehouse_id,
                ]);

                $quantityChange = $return->quantity;
                $quantityBefore = $inventory->quantity;
                $quantityAfter = $quantityBefore + $quantityChange;

                $inventory->update([
                    'quantity' => max(0, $quantityAfter),
                ]);

                // Log restock history
                RestockHistory::create([
                    'product_id' => $return->orderItem->product_id,
                    'warehouse_id' => $return->orderItem->warehouse_id,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => max(0, $quantityAfter),
                    'quantity_change' => $quantityChange,
                    'type' => 'in',
                    'reason' => 'return',
                    'reference_type' => 'return_order_item',
                    'reference_id' => $return->orderItem->order->order_number,
                    'performed_by' => Auth::id(),
                ]);

                // Notify the client that the return has been received
                Notification::create([
                    'user_id' => Auth::id(),
                    'client_id' => $return->orderItem->order->client_id,
                    'type' => 'success',
                    'title' => 'Return Received',
                    'message' => "Your return for Order #{$return->orderItem->order->order_number} has been received and processed.",
                    'url' => route('client.return-orders'),
                ]);

                $return->update([
                    'status' => 'received',
                    'processed_by' => Auth::id(),
                    'updated_at' => now(),
                ]);
            });

            $this->dispatch(
                'notify',
                variant: 'success',
                title: 'Success',
                message: 'Return received and inventory updated.'
            );
        } catch (\Exception $e) {
            Log::error('Error receiving return: '.$e->getMessage());
            // session()->flash('error', 'An error occurred while processing the return.');
            $this->dispatch(
                'notify',
                variant: 'danger',
                title: 'Error',
                message: 'An error occurred while processing the return.'
            );

            return;
        }
    }

    public function getReturnsProperty()
    {
        $query = ReturnItem::query()
            ->with(['orderItem.product', 'orderItem.order', 'processedBy'])
            ->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('orderItem.product', function ($qp) {
                    $qp->where('name', 'like', "%{$this->search}%")
                        ->orWhere('sku', 'like', "%{$this->search}%");
                })->orWhereHas('orderItem.order', function ($qo) {
                    $qo->where('order_number', 'like', "%{$this->search}%");
                });
            });
        }

        return $query->paginate($this->perPage);
    }

    public function getStatusBadgeColor($status)
    {
        return match ($status) {
            'requested' => 'yellow',
            'approved' => 'blue',
            'received' => 'green',
            'rejected' => 'red',
            default => 'zinc',
        };
    }

    public function render()
    {
        return view('livewire.admin.orders.return-manager', [
            'returns' => $this->returns,
        ]);
    }
}
