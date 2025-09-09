<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Product')]
class ProductView extends Component
{
  use WithFileUploads;

  public $product;
  public $productId;
  public $newPrimaryImage;
  public $newImage;

  public function mount($id)
  {
    $this->product = Product::with(['brand', 'category', 'inventoryLevels.warehouse', 'images'])
      ->findOrFail($id);
  }

  public function updatedNewPrimaryImage()
  {
    $this->validate([
      'newPrimaryImage' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Delete old image if it exists
    if ($this->product->primaryImage && Storage::disk('public')->exists($this->product->primaryImage->url)) {
      Storage::disk('public')->delete($this->product->primaryImage->url);
    }

    $path = $this->newPrimaryImage->store("products/{$this->product->id}", 'public');

    // Store new image
    $this->product->primaryImage()->updateOrCreate(
      ['is_primary' => true],
      [
        'product_id' => $this->product->id,
        'url' => $path,
        'alt_text' => $this->product->name,
        'is_primary' => true,
      ],
    );

    $this->product->refresh();

    // Success notification
    $this->dispatch(
      'notify',
      variant: 'success',
      title: 'Primary Image Updated',
      message: 'The product image has been replaced successfully.'
    );
  }

  public function updatedNewImage()
  {
    $this->validate([
      'newImage' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Check if the product already has 4 images
    if($this->product->secondaryImages()->count() >= 4) {
      $this->dispatch(
        'notify',
        variant: 'error',
        title: 'Image Limit Reached',
        message: 'A product can have a maximum of 4 secondary images.'
      );

      $this->newImage = null;
      return;
    }

    $path = $this->newImage->store("products/{$this->product->id}", 'public');

    // Store new image
    $this->product->images()->create([
      'product_id' => $this->product->id,
      'url' => $path,
      'alt_text' => $this->product->name,
      'is_primary' => false,
    ]);

    $this->product->refresh();
    $this->newImage = null;

    // Success notification
    $this->dispatch(
      'notify',
      variant: 'success',
      title: 'Image Added',
      message: 'The product image has been added successfully.'
    );
  }

  public function deleteImage($imageId)
  {
    $this->validateImageId($imageId);

    $image = $this->product->images()->findOrFail($imageId);

    // Delete image file from storage
    if (Storage::disk('public')->exists($image->url)) {
      Storage::disk('public')->delete($image->url);
    }

    // Delete image record from database
    $image->delete();

    $this->product->refresh();

    // Success notification
    $this->dispatch(
      'notify',
      variant: 'success',
      title: 'Image Deleted',
      message: 'The product image has been deleted successfully.'
    );
  }

  public function confirmForceDelete($id)
  {
    $this->validateOnlyId($id);

    $this->productId = $id;

    Flux::modal('confirm-force-delete')->show();
  }

  public function forceDeleteProduct()
  {
    $this->validate([
      'productId' => 'required|exists:products,id',
    ]);

    Product::withTrashed()->findOrFail($this->productId)->forceDelete();

    $this->reset(['productId']);

    $this->dispatch(
      'notify',
      variant: 'success',
      title: 'Product Permanently Deleted',
      message: 'Product and its images have been removed.'
    );

    $this->dispatch('productDeleted');
    Flux::modal('confirm-force-delete')->close();

    // Redirect to the product manager page after deletion
    return redirect()->route('admin.products');
  }

  protected function validateOnlyId($id)
  {
    validator(
      ['id' => $id],
      ['id' => 'required|exists:products,id']
    )->validate();
  }

  public function validateImageId($id)
  {
    validator(
      ['id' => $id],
      ['id' => 'required|exists:product_images,id']
    )->validate();
  }

  public function render()
  {
    return view('livewire.admin.inventory.product-view');
  }
}
