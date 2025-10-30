<?php

namespace App\Livewire\Admin\Client;

use App\Models\Client;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Clients')]
class ClientIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $clientFilter = '';

    public $sortField = 'company_name';

    public $sortDirection = 'asc';

    public $showModal = false;

    // protected $listeners = ['clientSaved' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingClientFilter()
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

    public function getClientsProperty()
    {
        $query = Client::query()->with(['user', 'orders']);

        if ($this->search) {
            $query->where('company_name', 'like', "%{$this->search}%")
                ->orWhere('contact_name', 'like', "%{$this->search}%")
                ->orWhere('contact_email', 'like', "%{$this->search}%");
        }

        if ($this->clientFilter) {
            $query->where('status', $this->clientFilter);
        }

        return $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function approve($clientId)
    {
        $client = Client::with('user')->findOrFail($clientId);
        $client->update(['status' => 'active']);
        $client->user->update(['approval_status' => 'active']);

        Session::flash('message', 'Client approved successfully.');
    }

    public function reject($clientId)
    {
        $client = Client::with('user')->findOrFail($clientId);
        $client->update(['status' => 'rejected']);
        $client->user->update(['approval_status' => 'rejected']);

        Session::flash('message', 'Client rejected.');
    }

    public function delete($clientId)
    {
        $client = Client::with('user')->findOrFail($clientId);
        $client->delete();
        $client->user->delete();

        Session::flash('message', 'Client deleted successfully.');

        $this->dispatch(
            'notify',
            variant: 'success',
            title: 'Success',
            message: 'Client deleted successfully.'
        );
    }

    public function render()
    {
        return view('livewire.admin.client.client-index', [
            'clients' => $this->clients,
        ]);
    }
}
