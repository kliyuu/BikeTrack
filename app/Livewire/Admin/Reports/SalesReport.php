<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Sales Report')]
class SalesReport extends Component
{
    public $dateFrom;

    public $dateTo;

    public $dateRange = [];

    public $period = 'month'; // today, week, month, quarter, year, custom

    public $chartType = 'sales'; // sales, orders, products

    public function mount()
    {
        $this->setDateRange();
    }

    public function updatedPeriod()
    {
        $this->setDateRange();
        $this->dispatch('updateSalesChart', $this->dailySales);
    }

    public function getSalesDataProperty()
    {
        $query = Order::query()
            ->with('items')
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $orders = $query->get();

        $totalSales = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        $totalItems = $orders->sum(fn ($order) => $order->items->sum('quantity'));

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
            'total_items' => $totalItems,
        ];
    }

    public function getTopProductsProperty()
    {
        $query = OrderItem::with(['product.primaryImage', 'productVariant'])
            ->whereHas('order', function ($orderQuery) {
                $orderQuery->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);

                if ($this->dateFrom) {
                    $orderQuery->whereDate('created_at', '>=', $this->dateFrom);
                }
                if ($this->dateTo) {
                    $orderQuery->whereDate('created_at', '<=', $this->dateTo);
                }
            })
            ->whereHas('product') // Ensure product exists
            ->selectRaw('product_id, product_variant_id, SUM(quantity) as total_quantity, SUM(quantity * unit_price) as total_revenue')
            ->groupBy('product_id', 'product_variant_id')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return $query;
    }

    public function getTopClientsProperty()
    {
        $query = Order::with('client')
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->selectRaw('client_id, COUNT(*) as total_orders, SUM(total_amount) as total_spent')
            ->groupBy('client_id')
            ->orderByDesc('total_spent');

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->limit(10)->get();
    }

    public function getDailySalesProperty()
    {
        $query = Order::query()
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);

        // Group differently depending on the selected period
        switch ($this->period) {
            case 'year':
            case 'quarter':
                // $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, COUNT(*) as order_count, SUM(total_amount) as total_sales');
                $query->selectRaw("strftime('%Y-%m', created_at) as period, COUNT(*) as order_count, SUM(total_amount) as total_sales");
                $groupBy = 'period';
                break;

            default: // today, week, month, custom
                // $query->selectRaw('DATE(created_at) as period, COUNT(*) as order_count, SUM(total_amount) as total_sales');
                $query->selectRaw("strftime('%Y-%m-%d', created_at) as period, COUNT(*) as order_count, SUM(total_amount) as total_sales");
                $groupBy = 'period';
                break;
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $rawData = $query->groupBy($groupBy)
            ->orderBy($groupBy)
            ->get()
            ->mapWithKeys(fn ($item) => [$item->period => $item]);

        // Use $dateRange (built in setDateRange) as labels
        return collect($this->dateRange)->map(function ($label) use ($rawData) {
            $key = $this->period === 'year' || $this->period === 'quarter'
              ? Carbon::parse($label.' 1 '.Carbon::now()->year)->format('Y-m') // month key
              : $label; // daily key

            $data = $rawData[$key] ?? null;

            return [
                'label' => $this->period === 'year' || $this->period === 'quarter'
                  ? $label // e.g. "January"
                  : Carbon::parse($label)->format('M j'), // e.g. "Sep 17"
                'order_count' => $data->order_count ?? 0,
                'total_sales' => $data->total_sales ?? 0,
            ];
        })->values();
    }

    public function getOrderStatusBreakdownProperty()
    {
        $query = Order::query()->selectRaw('status, COUNT(*) as count, SUM(total_amount) as total_amount');

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->groupBy('status')->get();
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

    private function setDateRange()
    {
        switch ($this->period) {
            case 'today':
                $this->dateFrom = Carbon::today()->toDateString();
                $this->dateTo = Carbon::today()->toDateString();
                $this->dateRange = [$this->dateFrom]; // single date
                break;

            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $this->dateFrom = $start->toDateString();
                $this->dateTo = $end->toDateString();
                $this->dateRange = collect(CarbonPeriod::create($start, $end))
                    ->map(fn ($date) => $date->toDateString())
                    ->toArray();
                break;

            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $this->dateFrom = $start->toDateString();
                $this->dateTo = $end->toDateString();
                $this->dateRange = collect(CarbonPeriod::create($start, $end))
                    ->map(fn ($date) => $date->toDateString())
                    ->toArray();
                break;

            case 'quarter':
                $start = Carbon::now()->startOfQuarter();
                $end = Carbon::now()->endOfQuarter();
                $this->dateFrom = $start->toDateString();
                $this->dateTo = $end->toDateString();
                $this->dateRange = collect(range(0, 2))
                    ->map(fn ($i) => $start->copy()->addMonths($i)->format('F'))
                    ->toArray();
                break;

            case 'year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $this->dateFrom = $start->toDateString();
                $this->dateTo = $end->toDateString();
                $this->dateRange = collect(range(1, 12))
                    ->map(fn ($month) => Carbon::createFromDate($start->year, $month, 1)->format('F'))
                    ->toArray();
                break;
        }
    }

    public function render()
    {
        return view('livewire.admin.reports.sales-report', [
            'salesData' => $this->salesData,
            'topProducts' => $this->topProducts,
            'topClients' => $this->topClients,
            'dailySales' => $this->dailySales,
            'orderStatusBreakdown' => $this->orderStatusBreakdown,
        ]);
    }
}
