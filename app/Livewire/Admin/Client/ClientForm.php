<?php

namespace App\Livewire\Admin\Client;

use App\Models\Client;
use Flux\Flux;
use Livewire\Component;

class ClientForm extends Component
{
    public $clientId;

    public $name;

    public $email;

    public $showModal = false;

    public $code = '';

    public $address = '';

    public $city = '';

    public $state = '';

    public $postal_code = '';

    public $country = '';

    public $contact_person = '';

    public $contact_phone = '';

    public $tax_number = '';

    public $payment_terms = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:20|unique:clients,code',
        'address' => 'required|string|max:500',
        'city' => 'required|string|max:100',
        'state' => 'nullable|string|max:100',
        'postal_code' => 'nullable|string|max:20',
        'country' => 'required|string|max:100',
        'contact_person' => 'required|string|max:255',
        'contact_phone' => 'nullable|string|max:20',
        'contact_email' => 'required|email|max:255',
        'tax_number' => 'nullable|string|max:50',
        'payment_terms' => 'nullable|string|max:255',
    ];

    protected $listeners = ['openClientModal'];

    public function openClientModal($id = null)
    {
        $this->resetValidation();
        $this->reset();

        $this->clientId = $id;

        if ($id) {
            $client = Client::findOrFail($id);
            $this->name = $client->contact_name;
            $this->email = $client->contact_email;
        }

        Flux::modal('client-modal')->show();
    }

    public function closeModal()
    {
        $this->resetForm();
        Flux::modal('client-modal')->close();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        Client::updateOrCreate(
            ['id' => $this->clientId],
            ['name' => $this->name, 'email' => $this->email]
        );

        $this->dispatch('clientSaved'); // notify ClientIndex
        Flux::modal('client-modal')->close();
    }

    private function resetForm()
    {
        $this->clientId = null;
        $this->name = '';
        $this->code = '';
        $this->address = '';
        $this->city = '';
        $this->state = '';
        $this->postal_code = '';
        $this->country = '';
        $this->contact_person = '';
        $this->contact_phone = '';
        $this->email = '';
        $this->tax_number = '';
        $this->payment_terms = '';
    }

    public function render()
    {
        return view('livewire.admin.client.client-form');
    }
}
