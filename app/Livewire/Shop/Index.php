<?php

namespace App\Livewire\Shop;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.shop')]
#[Title('BikeTrack Shop')]
class Index extends Component
{
  public function getCategoriesProperty()
  {
    return Category::query()->take(3)->get();
  }

  public function render()
  {
    return view('livewire.shop.index', [
      'categories' => $this->categories
    ]);
  }
}
