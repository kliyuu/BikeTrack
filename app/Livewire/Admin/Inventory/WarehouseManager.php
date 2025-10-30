<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Warehouse;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Warehouses')]
class WarehouseManager extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $perPage = 10;

    public $warehouseId = null;

    public $name = '';

    public $location = '';

    public $description = '';

    public $contact_person = '';

    public $contact_number = '';

    public $contact_email = '';

    public function updatingSearch($id)
    {
        $this->resetPage();
    }

    public function openWarehouseModal($id = null)
    {
        $this->warehouseId = $id;

        if ($id) {
            $warehouse = Warehouse::findOrFail($id);
            $this->fill($warehouse->toArray());
        }

        Flux::modal('warehouse-modal')->show();
    }

    public function saveWarehouse()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $data = $this->only([
            'name',
            'location',
            'description',
            'contact_person',
            'contact_number',
            'contact_email',
        ]);

        try {
            $warehouse = Warehouse::updateOrCreate(['id' => $this->warehouseId], $data);

            $this->dispatch(
                'notify',
                variant: 'success',
                title: 'Success',
                message: 'Warehouse saved successfully',
            );

            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                variant: 'error',
                title: 'Error',
                message: $e->getMessage(),
            );
        }
    }

    public function confirmDelete($id)
    {
        $this->validateIdOnly($id);
        $this->warehouseId = $id;
        Flux::modal('delete-warehouse')->show();
    }

    public function deleteWarehouse()
    {
        $this->validate([
            'warehouseId' => 'required|exists:warehouses,id',
        ]);

        try {
            Warehouse::where('id', $this->warehouseId)->delete();

            $this->dispatch(
                'notify',
                variant: 'success',
                title: 'Success',
                message: 'Warehouse deleted successfully',
            );

            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                variant: 'error',
                title: 'Error',
                message: $e->getMessage(),
            );
        }
    }

    public function closeModal()
    {
        $this->resetForm();
        Flux::modals()->close();
    }

    public function getWarehousesProperty()
    {
        $query = Warehouse::query()->withCount('inventoryLevels');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('location', 'like', "%{$this->search}%");
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    private function validateIdOnly($id)
    {
        validator(
            ['id' => $id],
            ['id' => 'required|exists:warehouses,id']
        )->validate();
    }

    private function resetForm()
    {
        $this->reset([
            'warehouseId',
            'name',
            'description',
            'location',
            'contact_person',
            'contact_number',
            'contact_email',
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.inventory.warehouse-manager', [
            'warehouses' => $this->warehouses,
        ]);
    }
}
