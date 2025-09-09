<?php

namespace App\Livewire\Admin;

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
  public $totalProducts;
  public $ordersToday;
  public $totalSalesToday;
  public $lowStockCount;
  public $totalClients;
  public $pendingOrders;
  public $totalRevenue;
  public $topProducts;
  public $recentOrders;
  public $stockAlerts;

  protected $listeners = [
    'refreshDashboard' => '$refresh',
  ];

  public function mount()
  {
    $this->loadStatistics();
  }

  public function loadStatistics()
  {
    $this->totalProducts = Product::count();
    $this->totalClients = Client::count();

    $today = Carbon::today();
    $this->ordersToday = Order::query()
      ->whereDate('created_at', $today)->count();
    $this->totalSalesToday = Order::query()
      ->whereDate('created_at', $today)
      ->where('status', '!=', 'cancelled')
      ->sum('total_amount');

    $this->lowStockCount = Product::query()
      ->whereColumn('cached_stock', '<=', 'low_stock_threshold')
      ->count();

    $this->stockAlerts = Product::query()
      ->whereColumn('cached_stock', '<=', 'low_stock_threshold')
      ->with(['category', 'brand'])
      ->take(5)
      ->get();

    $this->pendingOrders = Order::query()
      ->where('status', 'pending')
      ->count();

    $this->totalRevenue = Order::query()
      ->where('created_at', '>=', Carbon::now()->subDays(30))
      ->where('status', '!=', 'cancelled')
      ->sum('total_amount');

    $this->topProducts = Product::query()
      ->withCount('orderItems')
      ->orderBy('order_items_count', 'desc')
      ->take(5)
      ->get();

    $this->recentOrders = Order::query()
      ->with(['client'])
      ->orderBy('created_at', 'desc')
      ->take(5)
      ->get();
  }

  public function refreshStats()
  {
    $this->loadStatistics();
    $this->dispatch('refreshed');
  }

  public function render()
  {
    return view('livewire.admin.dashboard');
  }
}
