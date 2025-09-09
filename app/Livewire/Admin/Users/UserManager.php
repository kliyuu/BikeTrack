<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('User Management')]
class UserManager extends Component
{
  use WithPagination;

  public $search = '';
  public $roleFilter = '';
  public $statusFilter = '';
  public $sortBy = 'name';
  public $sortDirection = 'asc';

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function updatingRoleFilter()
  {
    $this->resetPage();
  }

  public function updatingStatusFilter()
  {
    $this->resetPage();
  }

  public function sortBy($field)
  {
    if ($this->sortBy === $field) {
      $this->sortDirection = $this->sortDirection === "asc" ? "desc" : "asc";
    } else {
      $this->sortBy = $field;
      $this->sortDirection = "asc";
    }
  }

  public function getUsersProperty()
  {
    $query = User::query()
      ->with('role')
      ->where('role_id', '!=', 3); // Exclude clients

    if ($this->search) {
      $query->where(function ($q) {
        $q->where('name', 'like', '%' . $this->search . '%')
          ->orWhere('email', 'like', '%' . $this->search . '%');
      });
    }

    if ($this->roleFilter) {
      $query->where('role_id', $this->roleFilter);
    }

    if ($this->statusFilter) {
      $query->where('status', $this->statusFilter);
    }

    return $query->orderBy($this->sortBy, $this->sortDirection)->paginate(10);
  }

  public function render()
  {
    return view('livewire.admin.users.user-manager', [
      'users' => $this->users,
    ]);
  }
}
