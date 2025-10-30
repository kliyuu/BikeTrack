<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use App\Services\StockNotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Admin extends Component
{
    public $unreadCount = 0;

    public $notifications = [];

    protected $listeners = [
        'refreshNotifications' => '$refresh',
    ];

    public function mount(StockNotificationService $stockService)
    {
        // Check for stock notifications when component is loaded
        $stockService->checkOutOfStockProducts();
        $stockService->checkLowStockProducts();

        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $userId = Auth::id();

        $this->unreadCount = Notification::query()
            ->where('client_id', null)
            ->where('is_read', false)
            ->count();

        $this->notifications = Notification::query()
            ->where('client_id', null)
            ->latest()
            ->take(10)
            ->get()
            ->toArray();
    }

    public function checkStockNotifications()
    {
        $stockService = app(StockNotificationService::class);
        $stockService->checkOutOfStockProducts();
        $stockService->checkLowStockProducts();

        $this->loadNotifications();

        $this->dispatch('showToast', [
            'type' => 'success',
            'message' => 'Stock notifications updated successfully!',
        ]);
    }

    public function markAsReadAndRedirect($id)
    {
        $notification = Notification::where('id', $id)
            ->where('client_id', null)
            ->first();

        if ($notification) {
            if (! $notification->is_read) {
                $notification->update(['is_read' => true]);
            }

            // redirect user to the URL if it exists
            if ($notification->url) {
                return redirect()->to($notification->url);
            }
        }

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notifications.admin');
    }
}
