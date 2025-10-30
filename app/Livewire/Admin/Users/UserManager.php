<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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

    public $sortBy = 'id';

    public $sortDirection = 'asc';

    public $perPage = 10;

    public $userId;

    public string $name = '';

    public string $email = '';

    public string $role_id = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $approval_status = '';

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
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openUserModal($id = null)
    {
        $this->resetForm();

        $this->userId = $id;

        if ($id) {
            $user = User::findOrFail($id);
            $this->fill($user->toArray());
        }

        Flux::modal('user-modal')->show();
    }

    public function saveUser()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$this->userId,
            'role_id' => 'required|exists:roles,id',
            'password' => $this->userId ? [
                'nullable',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->letters()
                    ->symbols(),
            ] : [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->letters()
                    ->symbols(),
            ],
            'approval_status' => 'nullable|in:active,inactive,pending',
        ]);

        if ($this->userId) {
            $user = User::findOrFail($this->userId);

            $user->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role_id' => $validatedData['role_id'],
                'password' => $validatedData['password'] ? Hash::make($validatedData['password']) : $user->password,
                'approval_status' => $validatedData['approval_status'],
            ]);

            $this->dispatch(
                'notify',
                variant: 'success',
                title: 'Success',
                message: 'User updated successfully.',
            );
        } else {
            $newUser = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role_id' => $validatedData['role_id'],
                'password' => Hash::make($validatedData['password']),
                'approval_status' => 'active',
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
                'email_verified_at' => Carbon::now(),
            ]);

            dump($newUser);

            $this->dispatch(
                'notify',
                variant: 'success',
                title: 'Success',
                message: 'User created successfully.',
            );
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->validateOnlyId($id);
        $this->userId = $id;

        Flux::modal('delete-user')->show();
    }

    public function deleteUser()
    {
        $this->validateOnlyId($this->userId);

        $user = User::findOrFail($this->userId);
        $user->delete();

        $this->dispatch(
            'notify',
            variant: 'success',
            title: 'Deleted',
            message: 'User deleted successfully.',
        );

        $this->closeModal();
    }

    public function getUsersProperty()
    {
        $query = User::query()
            ->with('role')
            ->where('role_id', '!=', 3); // Exclude clients

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
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

    public function closeModal()
    {
        $this->resetForm();
        Flux::modals()->close();
    }

    public function getStatusBadgeColor($status)
    {
        return match ($status) {
            'active' => 'green',
            'inactive' => 'red',
            'pending' => 'yellow',
            default => 'gray',
        };
    }

    protected function validateOnlyId($id)
    {
        validator(
            ['id' => $id],
            ['id' => 'required|exists:users,id']
        )->validate();
    }

    private function resetForm()
    {
        $this->reset([
            'userId',
            'name',
            'email',
            'role_id',
            'password',
            'password_confirmation',
            'approval_status',
        ]);

        // Reset the validation state
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.users.user-manager', [
            'users' => $this->users,
        ]);
    }
}
