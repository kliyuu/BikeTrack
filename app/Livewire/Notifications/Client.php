<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Client extends Component
{
    public $notifications = [];

    protected $listeners = [
        'orderUpdated' => '$refresh',
        'returnRequestUpdated' => '$refresh',
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $clientId = Auth::user()->client->id;

        $this->notifications = Notification::query()
            ->where('client_id', $clientId)
            ->latest()
            ->take(10)
            ->get()
            ->toArray();
    }

    public function getUnreadCountProperty()
    {
        return Notification::query()
            ->where('client_id', Auth::user()->client->id)
            ->where('is_read', false)
            ->count();
    }

    public function markAsReadAndRedirect($id)
    {
        $notification = Notification::where('id', $id)
            ->where('client_id', Auth::user()->client->id)
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
        return view('livewire.notifications.client', [
            'unreadCount' => $this->unreadCount,
        ]);
    }
}
