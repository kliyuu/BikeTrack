<?php

namespace App\Livewire\Shop;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.shop')]
#[Title('Product Catalog')]
class ProductCatalog extends Component
{
  use WithPagination;

  public $search = '';
  public $sortField = 'name';
  public $sortDirection = 'asc';
  public $perPage = 15;

  #[Url(as: 'category', except: '')]
  public $categoryFilter = '';
  public $brandFilter = [];
  public $priceMinFilter = '';
  public $priceMaxFilter = '';

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function updatingCategoryFilter()
  {
    $this->resetPage();
  }

  public function updatingBrandFilter()
  {
    $this->resetPage();
  }

  public function updatingSortField()
  {
    $this->resetPage();
  }

  public function getProductsProperty()
  {
    $query = Product::query()
      ->active()
      ->with(['category', 'brand', 'primaryImage']);

    if ($this->categoryFilter) {
      $query->where('category_id', $this->categoryFilter);
    }

    if (!empty($this->brandFilter)) {
      $query->whereIn('brand_id', $this->brandFilter);
    }

    switch ($this->sortField) {
      case 'newest':
        $query->orderBy('created_at', 'desc');
        break;
      case 'price_asc':
        $query->orderBy('unit_price', 'asc');
        break;
      case 'price_desc':
        $query->orderBy('unit_price', 'desc');
        break;
      case 'name':
      default:
        $query->orderBy($this->sortField, $this->sortDirection);
        break;
    }

    // $query->orderBy($this->sortField, $this->sortDirection);

    return $query->paginate($this->perPage);
  }

  public function getCategoriesProperty()
  {
    return Category::query()->orderBy('name')->get();
  }

  public function getBrandsProperty()
  {
    return Brand::query()->orderBy('name')->get();
  }

  public function render()
  {
    return view('livewire.shop.product-catalog', [
      'products' => $this->products,
      'categories' => $this->categories,
      'brands' => $this->brands,
    ]);
  }
}
