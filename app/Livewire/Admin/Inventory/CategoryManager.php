<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Category;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Categories')]
class CategoryManager extends Component
{
  use WithPagination, WithFileUploads;

  public $search = '';
  public $sortField = 'name';
  public $sortDirection = 'asc';

  public $categoryId = null;
  public $name = '';
  public $description = '';
  public $perPage = 10;

  public $currentImage; // Existing image path
  public $image; // New image file upload

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function sortBy($field)
  {
    if ($this->sortField === $field) {
      $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
      $this->sortField = $field;
      $this->sortDirection = 'asc';
    }

    $this->resetPage();
  }

  public function openCategoryModal($id = null)
  {
    $this->resetForm();

    $this->validateOnlyId($id);

    $this->categoryId = $id;

    if ($id) {
      $category = Category::findOrFail($id);

      $this->name = $category->name;
      $this->description = $category->description;
      $this->image = $category->image;
      $this->currentImage = $category->image;
    }

    Flux::modal('category-modal')->show();
  }

  public function saveCategory()
  {
    $this->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'image' => 'nullable|image|max:2048', // Validate image max 2MB
    ]);

    $data = [
      'name' => $this->name,
      'description' => $this->description,
      'slug' => $this->generateCategorySlug($this->name, $this->categoryId),
    ];

    try {
      if ($this->categoryId) {
        $category = Category::findOrFail($this->categoryId);

        if ($this->image) {
          // Delete old image if exists
          if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
          }

          // Store new image
          $data['image'] = $this->image->store("categories/{$category->id}", 'public');
        }

        $category->update($data);
      } else {
        $category = Category::create($data);
        $category->image = $this->image->store("categories/{$category->id}", 'public') ?? null;
        $category->save();
      }

      $this->dispatch(
        'notify',
        variant: 'success',
        title: $this->categoryId ? 'Category Updated' : 'Category Created',
        message: $this->categoryId ? 'Category has been updated.' : 'Category has been created.',
      );

      $this->closeModal();
    } catch (\Exception $e) {
      $this->dispatch(
        'notify',
        variant: 'error',
        title: 'Error',
        message: $e->getMessage()
      );
    }
  }

  public function confirmDelete($id)
  {
    $this->validateOnlyId($id);
    $this->categoryId = $id;

    Flux::modal('delete-category')->show();
  }

  public function deleteCategory()
  {
    $this->validate([
      'categoryId' => 'required|exists:categories,id',
    ]);

    try {
      // Soft delete the category
      $category = Category::where('id', $this->categoryId)->first();
      $category->delete();

      $this->reset(['categoryId']);

      $this->dispatch(
        'notify',
        variant: 'success',
        title: 'Category Deleted',
        message: 'Category has been deleted.',
      );

      $this->closeModal();
    } catch (\Exception $e) {
      $this->dispatch(
        'notify',
        variant: 'error',
        title: 'Error',
        message: $e->getMessage()
      );
    }
  }

  public function closeModal()
  {
    $this->resetForm();
    Flux::modals()->close();
  }

  public function getCategoriesProperty()
  {
    $query = Category::query()->withCount('products');

    if ($this->search) {
      $query->where('name', 'like', "%{$this->search}%");
    }

    return $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
  }

  private function generateCategorySlug($name, $ignoreId = null)
  {
    $slug = Str::slug($name);
    $originalSlug = $slug;
    $counter = 1;

    while (
      Category::where('slug', $slug)
        ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
        ->exists()
    ) {
      $slug = "{$originalSlug}-{$counter}";
      $counter++;
    }

    return $slug;
  }

  private function validateOnlyId($id)
  {
    validator(
      ['id' => $id],
      ['id' => 'required|exists:categories,id']
    )->validate();
  }

  private function resetForm()
  {
    $this->reset([
      'categoryId',
      'name',
      'description',
      'image',
      'currentImage',
    ]);

    $this->resetValidation();
  }

  public function render()
  {
    return view('livewire.admin.inventory.category-manager', [
      'categories' => $this->categories
    ]);
  }
}
