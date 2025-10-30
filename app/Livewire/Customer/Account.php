<?php

namespace App\Livewire\Customer;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.shop')]
#[Title('Account')]
class Account extends Component
{
    public $user;

    public $name;

    public $email;

    public $currentPassword;

    public $newPassword;

    public $confirmPassword;

    public $companyName = '';

    public $contactName = '';

    public $contactEmail = '';

    public $contactPhone = '';

    public $billingAddress = '';

    public $taxNumber = '';

    public function mount()
    {
        $this->user = User::with('client')->find(Auth::id());

        $userData = $this->user;

        $this->name = $userData->name;
        $this->email = $userData->email;
        $this->companyName = $userData->client->company_name ?? '';
        $this->contactName = $userData->client->contact_name ?? '';
        $this->contactEmail = $userData->client->contact_email ?? '';
        $this->contactPhone = $userData->client->contact_phone ?? '';
        $this->billingAddress = $userData->client->billing_address ?? '';
        $this->taxNumber = $userData->client->tax_number ?? '';
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$this->user->id,
        ]);

        $data = $this->only(['name', 'email']);

        $this->user->update($data);

        $this->dispatch('profile-updated', name: $this->user->name);

        Session::flash('success', 'Profile updated successfully.');
    }

    public function updatePassword()
    {
        $this->validate([
            'currentPassword' => 'required|current_password',
            'newPassword' => 'required|min:8',
            'confirmPassword' => 'required|same:newPassword',
        ]);

        $this->user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        Auth::login($this->user);

        $this->reset(['currentPassword', 'newPassword', 'confirmPassword']);

        $this->dispatch('profile-updated', name: $this->user->name);

        Session::flash('success', 'Password updated successfully.');
    }

    public function updateBillingDetails()
    {
        $this->validate([
            'companyName' => 'nullable|string|max:255',
            'contactName' => 'nullable|string|max:255',
            'contactEmail' => 'nullable|email|max:255',
            'contactPhone' => 'nullable|string|max:20',
            'billingAddress' => 'nullable|string|max:500',
            'taxNumber' => 'nullable|string|max:255',
        ]);

        $data = [
            'company_name' => $this->companyName,
            'contact_name' => $this->contactName,
            'contact_email' => $this->contactEmail,
            'contact_phone' => $this->contactPhone,
            'billing_address' => $this->billingAddress,
            'tax_number' => $this->taxNumber,
        ];

        $this->user->client->update($data);

        $this->dispatch('profile-updated', name: $this->user->name);

        Session::flash('success', 'Billing details updated successfully.');
    }

    public function getOrderStatsProperty()
    {
        $orders = $this->user->client?->orders()->get();

        return [
            'totalOrders' => $orders?->count() ?? 0,
            'pendingOrders' => $orders?->where('status', 'pending')->count() ?? 0,
            'completedOrders' => $orders?->where('status', 'delivered')->count() ?? 0,
            'lastOrderDate' => $orders?->sortByDesc('placed_at')->first()?->placed_at ?? null,
            'totalSpent' => $orders?->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                ->sum('total_amount') ?? 0,
        ];
    }

    public function getClientInfoProperty()
    {
        return $this->user->client ?? null;
    }

    public function render()
    {
        return view('livewire.customer.account', [
            'orderStats' => $this->orderStats,
            'clientInfo' => $this->clientInfo,
        ]);
    }
}
