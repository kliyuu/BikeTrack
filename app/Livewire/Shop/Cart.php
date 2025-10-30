<?php

namespace App\Livewire\Shop;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Cart extends Component
{
    public $shippingFee = 0; // Flat rate shipping fee for simplicity

    protected $listeners = [
        'productAddedToCart' => '$refresh', // Listen for product added event
        'cartUpdated' => '$refresh',
    ];

    public function updateCartQuantity($cartItemId, $itemQuantity)
    {
        $cartItem = Auth::user()->cartItems()->find($cartItemId);

        if (! $cartItem) {
            $this->dispatch(
                'notify',
                variant: 'error',
                title: 'Error',
                message: 'Cart item not found.'
            );

            return;
        }

        $itemQuantity = max(0, intval($itemQuantity));

        if ($itemQuantity === 0) {
            $cartItem->delete();
        } else {
            if ($itemQuantity > $cartItem->available_stock) {
                $this->dispatch(
                    'notify',
                    variant: 'error',
                    title: 'Error',
                    message: "Cannot set quantity to {$itemQuantity}. Only {$cartItem->available_stock} units available in stock."
                );

                return;
            }

            $cartItem->quantity = $itemQuantity;
            $cartItem->save();
        }

        $this->dispatch('cartUpdated');
    }

    public function removeFromCart($cartItemId)
    {
        if (Auth::check()) {
            $item = Auth::user()->cartItems()->where('id', $cartItemId)->first();

            if ($item) {
                $item->delete();

                $this->dispatch('cartUpdated');
                $this->dispatch(
                    'notify',
                    variant: 'success',
                    title: 'Removed from Cart',
                    message: "{$item->name} has been removed from your cart."
                );
            }
        }
    }

    public function getCartItemCountProperty()
    {
        return Auth::check() ? Auth::user()->cartItems()->sum('quantity') : 0;
    }

    public function getCartTotalProperty()
    {
        return Auth::check() ? Auth::user()->cartItems->sum(fn ($item) => $item->quantity * $item->price) : 0;
    }

    public function render()
    {
        return view('livewire.shop.cart', [
            'cartItems' => Auth::check() ? Auth::user()->cartItems : collect(),
            'cartItemCount' => $this->cartItemCount,
            'cartTotal' => $this->cartTotal,
        ]);
    }
}
