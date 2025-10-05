<?php

use function Livewire\Volt\{layout, title, state, computed, rules};
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

layout('layouts.app');
title('Gestión de Roles');

state(['name' => '', 'selectedPermissions' => []]);

rules(['name' => 'required|string|max:255|unique:roles,name']);

$roles = computed(function () {
    return Role::with('permissions')->get();
});

$permissions = computed(function () {
    return Permission::all();
});

$createRole = function () {
    $this->validate();
    
    if (!auth()->user()->can('roles.create')) {
        session()->flash('error', 'No tienes permisos para crear roles.');
        return;
    }

    $role = Role::create(['name' => $this->name]);
    $role->givePermissionTo($this->selectedPermissions);
    
    session()->flash('success', "Rol '{$this->name}' creado correctamente.");
    
    $this->reset(['name', 'selectedPermissions']);
};

$deleteRole = function ($roleId) {
    if (!auth()->user()->can('roles.delete')) {
        session()->flash('error', 'No tienes permisos para eliminar roles.');
        return;
    }

    $role = Role::findOrFail($roleId);
    
    if (in_array($role->name, ['admin', 'manager', 'user'])) {
        session()->flash('error', 'No puedes eliminar los roles predeterminados del sistema.');
        return;
    }
    
    $role->delete();
    session()->flash('success', 'Rol eliminado correctamente.');
};

?>

<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Lista de roles -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Roles del Sistema</h5>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Permisos</th>
                                        <th>Usuarios</th>
                                        @can('roles.delete')
                                            <th>Acciones</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($this->roles as $role)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($role->name) }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $role->permissions->count() }} permisos
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $role->users()->count() }} usuarios
                                                </small>
                                            </td>
                                            @can('roles.delete')
                                                <td>
                                                    @if (!in_array($role->name, ['admin', 'manager', 'user']))
                                                        <button 
                                                            class="btn btn-sm btn-outline-danger"
                                                            wire:click="deleteRole({{ $role->id }})"
                                                            wire:confirm="¿Estás seguro de eliminar este rol?"
                                                        >
                                                            Eliminar
                                                        </button>
                                                    @else
                                                        <small class="text-muted">Rol del sistema</small>
                                                    @endif
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @can('roles.create')
                <div class="col-md-4">
                    <!-- Formulario para crear rol -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Crear Nuevo Rol</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit="createRole">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre del Rol</label>
                                    <input 
                                        type="text" 
                                        class="form-control @error('name') is-invalid @enderror" 
                                        id="name"
                                        wire:model="name"
                                        placeholder="Ej: editor, moderador..."
                                    >
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Permisos</label>
                                    <div class="form-check-container" style="max-height: 300px; overflow-y: auto;">
                                        @foreach($this->permissions as $permission)
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    value="{{ $permission->name }}" 
                                                    id="permission-{{ $permission->id }}"
                                                    wire:model="selectedPermissions"
                                                >
                                                <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    Crear Rol
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</div>