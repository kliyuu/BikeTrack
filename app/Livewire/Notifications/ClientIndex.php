<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.shop')]
#[Title('Notifications')]
class ClientIndex extends Component
{
    use WithPagination;

    public $perPage = 15;

    public $filter = 'all'; // all, read, unread

    public function getNotificationsProperty()
    {
        $query = Notification::query()
            ->whereNotNull('client_id')
            ->where('client_id', Auth::user()->client->id)
            ->orderBy('created_at', 'desc');

        if ($this->filter === 'read') {
            $query->where('is_read', true);
        } elseif ($this->filter === 'unread') {
            $query->where('is_read', false);
        }

        return $query->paginate($this->perPage);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('client_id', Auth::user()->client->id)
            ->first();

        if ($notification && ! $notification->is_read) {
            $notification->update(['is_read' => true]);
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Notification marked as read.',
            ]);
        }
    }

    public function markAllAsRead()
    {
        Notification::where('client_id', Auth::user()->client->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->dispatch('showToast', [
            'type' => 'success',
            'message' => 'All notifications marked as read.',
        ]);
    }

    public function deleteNotification($id)
    {
        $notification = Notification::where('id', $id)
            ->where('client_id', Auth::user()->client->id)
            ->first();

        if ($notification) {
            $notification->delete();
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Notification deleted successfully.',
            ]);
        }
    }

    public function deleteAllRead()
    {
        Notification::where('client_id', Auth::user()->client->id)
            ->where('is_read', true)
            ->delete();

        $this->dispatch('showToast', [
            'type' => 'success',
            'message' => 'All read notifications deleted.',
        ]);
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function navigateToUrl($id, $url)
    {
        $this->markAsRead($id);

        if ($url) {
            return redirect()->to($url);
        }
    }

    public function render()
    {
        return view('livewire.notifications.client-index', [
            'notifications' => $this->notifications,
        ]);
    }
}
