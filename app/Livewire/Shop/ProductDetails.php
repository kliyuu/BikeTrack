<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.shop')]
#[Title('Product Details')]
class ProductDetails extends Component
{
  public $product;
  public $quantity = 1;

  public $viewMode = 'grid'; // Default view mode

  protected $listeners = [
    'cartUpdated' => '$refresh',
    'productAddedToCart' => '$refresh' // Listen for product added event
  ];

  public function mount($id)
  {
    $this->product = Product::with(['brand', 'category', 'inventoryLevels.warehouse'])
      ->findOrFail($id);
  }

  public function addToCart($productId, $variantId = null)
  {
    $this->validate([
      "quantity" => "required|integer|min:1|max:{$this->product->cached_stock}",
    ]);

    if(!$this->product) {
      $this->dispatch(
        'notify',
        variant: 'error',
        title: 'Error',
        message: "Product not found."
      );
      return;
    }

    $variantId = $variantId ?: null;
    $price = $this->product->unit_price;
    $availableStock = $this->product->cached_stock;
    $productName = $this->product->name;

    // TODO: Handle product variants if applicable
    if($variantId) {
      // Handle variant-specific logic if needed
    }

    $user = Auth::user();
    $cartItem = $user->cartItems()->firstOrNew([
      'product_id' => $productId,
      'product_variant_id' => $variantId,
    ]);

    $cartItem->fill([
      'name' => $productName,
      'sku' => $this->product->sku,
      'price' => $price,
      'image' => $this->product->primaryImage->url,
      'available_stock' => $availableStock,
    ]);

    $cartItem->quantity += $this->quantity;

    if($cartItem->quantity > $availableStock) {
      $this->dispatch(
        'notify',
        variant: 'error',
        title: 'Error',
        message: "Cannot add {$this->quantity} units. Only {$availableStock} units available in stock."
      );
      return;
    }

    $cartItem->save();

    $this->dispatch('productAddedToCart');
    $this->dispatch(
      'notify',
      variant: 'success',
      title: 'Added to Cart',
      message: "Added {$this->quantity} units of {$this->product->name} to the cart."
    );
  }

  public function updateCartQuantity($cartItemId, $itemQuantity)
  {
    $this->validate([
      "itemQuantity" => "required|integer|min:1",
    ]);

    $cartItem = Auth::user()->cartItems()->find($cartItemId);

    if (!$cartItem) {
      $this->dispatch(
        'notify',
        variant: 'error',
        title: 'Error',
        message: "Cart item not found."
      );
      return;
    }

    $itemQuantity = max(0, intval($itemQuantity));
    if($itemQuantity === 0) {
      $cartItem->delete();
    } else {
      if($itemQuantity > $cartItem->available_stock) {
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

  public function getCartItemCountProperty()
  {
    return Auth::check() ? Auth::user()->cartItems()->sum('quantity') : 0;
  }

  public function getCartTotalProperty()
  {
    return Auth::check() ? Auth::user()->cartItems->sum(fn($item) => $item->price * $item->quantity) : 0;
  }

  public function render()
  {
    return view('livewire.shop.product-details', [
      'cartItems' => Auth::check() ? Auth::user()->cartItems : collect(),
      'cartItemCount' => $this->cartItemCount,
      'cartTotal' => $this->cartTotal
    ]);
  }
}
