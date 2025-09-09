<?php

namespace App\Livewire\Customer;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Account')]
class Account extends Component
{
    public function render()
    {
        return view('livewire.customer.account');
    }
}
