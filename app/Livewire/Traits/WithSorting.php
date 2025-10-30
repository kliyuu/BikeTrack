<?php

namespace App\Livewire\Traits;

trait WithSorting
{
    public $sortField = 'name'; // Default sort field

    public $sortDirection = 'asc'; // Default sort direction

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
}
