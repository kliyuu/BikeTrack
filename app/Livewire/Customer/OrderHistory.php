<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.shop')]
#[Title('Order History')]
class OrderHistory extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $dateFrom = '';

    public $dateTo = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function getOrdersProperty()
    {
        $query = Order::with(['items.product'])
            ->where('client_id', Auth::user()->client->id)
            ->select('orders.*');

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('orders.id', 'like', "%{$this->search}%")
                    ->orWhere('orders.order_number', 'like', "%{$this->search}%")
                    ->orWhereHas('items.product', function ($productQuery) {
                        $productQuery->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Apply date filters
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function getOrderStatsProperty()
    {
        $client = Auth::user()->client;

        if (! $client) {
            return [
                'totalOrders' => 0,
                'pendingOrders' => 0,
                'confirmedOrders' => 0,
                'shippedOrders' => 0,
                'completedOrders' => 0,
                'totalSpent' => 0,
            ];
        }

        $stats = $client->orders()
            ->selectRaw("
            COUNT(*) as totalOrders,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pendingOrders,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmedOrders,
            SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shippedOrders,
            SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completedOrders,
            SUM(CASE WHEN status IN ('delivered', 'completed')
                     THEN total_amount ELSE 0 END) as totalSpent
        ")
            ->first();

        return [
            'totalOrders' => $stats->totalOrders ?? 0,
            'pendingOrders' => $stats->pendingOrders ?? 0,
            'confirmedOrders' => $stats->confirmedOrders ?? 0,
            'shippedOrders' => $stats->shippedOrders ?? 0,
            'completedOrders' => $stats->completedOrders ?? 0,
            'totalSpent' => $stats->totalSpent ?? 0,
        ];
    }

    public function getStatusBadgeColor(string $status)
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
        return view('livewire.customer.order-history', [
            'orders' => $this->orders,
            'orderStats' => $this->orderStats,
        ]);
    }
}
