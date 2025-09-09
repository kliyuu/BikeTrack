<?php

namespace App\Livewire\Shop;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartIcon extends Component
{
  protected $listeners = [
    'productAddedToCart' => '$refresh', // Listen for product added event
    'cartUpdated' => '$refresh'
  ];

  public function getCartItemCountProperty()
  {
    return Auth::check() ? Auth::user()->cartItems()->count() : 0;
  }

  public function render()
  {
    return view('livewire.shop.cart-icon', [
      'cartItemCount' => $this->cartItemCount,
    ]);
  }
}
