<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Brand;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Brands')]
class BrandManager extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $brandId = null;

    public $name = '';

    public $description = '';

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

    public function openBrandModal($id = null)
    {
        $this->resetForm();

        $this->brandId = $id;

        if ($id) {
            $brand = Brand::findOrFail($id);
            $this->name = $brand->name;
            $this->description = $brand->description;
        }

        Flux::modal('brand-modal')->show();
    }

    public function saveBrand()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $brand = Brand::updateOrCreate(
                ['id' => $this->brandId],
                [
                    'name' => $this->name,
                    'description' => $this->description,
                    'slug' => $this->generateBrandSlug($this->name, $this->brandId),
                ]
            );

            $this->dispatch(
                'notify',
                variant: 'success',
                title: $this->brandId ? 'Brand Updated' : 'Brand Created',
                message: $this->brandId ? 'Brand has been updated.' : 'Brand has been created.',
            );

            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                variant: 'error',
                title: 'Error',
                message: $e->getMessage()
            );

            return;
        }
    }

    public function confirmDelete($id)
    {
        $this->validateOnlyId($id);
        $this->brandId = $id;

        Flux::modal('delete-brand')->show();
    }

    public function deleteBrand()
    {
        $this->validate([
            'brandId' => 'required|exists:brands,id',
        ]);

        try {
            // Soft delete the brand
            $brand = Brand::where('id', $this->brandId)->first();
            $brand->delete();

            $this->reset(['brandId']);

            $this->dispatch(
                'notify',
                variant: 'success',
                title: 'Brand Deleted',
                message: 'Brand has been deleted.',
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

    public function getBrandsProperty()
    {
        $query = Brand::query()->withCount('products');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        return $query->orderBy($this->sortField, $this->sortDirection)->paginate(10);
    }

    private function resetForm()
    {
        $this->reset([
            'brandId',
            'name',
            'description',
        ]);

        $this->resetValidation();
    }

    private function generateBrandSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (
            Brand::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
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
            ['id' => 'required|exists:brands,id']
        )->validate();
    }

    public function render()
    {
        return view('livewire.admin.inventory.brand-manager', [
            'brands' => $this->brands,
        ]);
    }
}
