<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class UserManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedRole = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedRole()
    {
        $this->resetPage();
    }

    public function assignRole($userId, $roleName)
    {
        if (!Auth::user()->can('users.edit')) {
            session()->flash('error', 'No tienes permisos para realizar esta acción.');
            return;
        }

        $user = User::findOrFail($userId);
        $user->syncRoles([$roleName]);

        session()->flash('success', "Rol '{$roleName}' asignado a {$user->name} correctamente.");
    }

    public function render()
    {
        $query = User::with('roles');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedRole) {
            $query->role($this->selectedRole);
        }

        $users = $query->paginate(10);
        $roles = Role::all();

        return view('livewire.user-manager', compact('users', 'roles'));
    }
}
