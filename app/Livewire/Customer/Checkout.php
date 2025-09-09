<?php

namespace App\Livewire\Customer;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
class Checkout extends Component
{
    public function render()
    {
        return view('livewire.customer.checkout');
    }
}
