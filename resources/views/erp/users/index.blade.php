@extends('layouts.vuexy')

@section('title', 'Gestión de Usuarios - ERP Multisoft')

@php
    $dataBreadcrumb = [
        'title' => 'Gestión de Usuarios',
        'description' => 'Administra los usuarios del sistema ERP Multisoft.',
        'icon' => 'ti ti-users',
        'breadcrumbs' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Usuarios', 'url' => route('users.index'), 'active' => true]
        ],
        'actions' => [
            // ['name' => 'Crear Usuario', 'url' => route('users.create'), 'icon' => 'ti ti-plus', 'permission' => 'users.create'],
            // ['name' => 'Importar Usuarios', 'url' => route('users.create'), 'icon' => 'ti ti-upload', 'permission' => 'users.create']
        ],
        'stats' => [
            ['name' => 'Total Usuarios', 'value' => $users->count(), 'icon' => 'ti ti-users', 'color' => 'bg-label-primary'],
            ['name' => 'Usuarios Activos', 'value' => $users->where('estado', true)->count(), 'icon' => 'ti ti-circle-check', 'color' => 'bg-label-success']
        ]
    ];

    $dataHeaderCard = [
        'title' => 'Lista de Usuarios',
        'description' => 'Gestiona usuarios y asigna roles',
        'textColor' => 'text-primary',
        'icon' => 'ti ti-users',
        'iconColor' => 'alert-primary',
        'actions' => [
            // [
            //     'typeAction' => 'btnToggle', // btnIdEvent, btnLink, btnToggle, btnInfo
            //     'name' => 'Crear Usuario',
            //     'url' => route('users.create'),
            //     'icon' => 'ti ti-plus',
            //     'permission' => 'users.create',
            //     'typeButton' => 'btn-primary' // btn-primary, btn-info, btn-success, btn-danger, btn-warning, btn-secondary
            // ],
            [
                'typeAction' => 'btnToggle', // btnIdEvent, btnLink, btnToggle, btnInfo
                'name' => 'Crear Usuario',
                'icon' => 'ti ti-plus',
                'permission' => 'users.create',
                'typeButton' => 'btn-primary',
                'idModal' => 'createUserModal' // necesario si es btnToggle
            ],
        ],
    ];

@endphp


@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

     @include('layouts.vuexy.breadcrumb', $dataBreadcrumb)

    <div class="row">
        <div class="col-12">
            <div class="card">

                @include('layouts.vuexy.header-card', $dataHeaderCard)

                <div class="card-body">
                    <!-- Componente Livewire con estilo Vuexy -->
                    <div class="table-responsive">
                        <livewire:user-manager />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mt-4">
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vuexy/img/avatars/4.png') }}" alt="usuarios" class="rounded">
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Usuarios Activos</span>
                    <h3 class="card-title mb-2">{{ App\Models\User::count() }}</h3>
                    <small class="text-success fw-semibold">
                        <i class="ti ti-arrow-up"></i> +100%
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vuexy/img/avatars/2.png') }}" alt="admins" class="rounded">
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Administradores</span>
                    <h3 class="card-title mb-2">{{ App\Models\User::role('admin')->count() }}</h3>
                    <small class="text-info fw-semibold">
                        <i class="ti ti-user-check"></i> Activos
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vuexy/img/avatars/4.png') }}" alt="managers" class="rounded">
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Gerentes</span>
                    <h3 class="card-title mb-2">{{ App\Models\User::role('manager')->count() }}</h3>
                    <small class="text-warning fw-semibold">
                        <i class="ti ti-user-share"></i> Gestión
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('vuexy/img/avatars/6.png') }}" alt="users" class="rounded">
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Usuario Regular</span>
                    <h3 class="card-title mb-2">{{ App\Models\User::role('user')->count() }}</h3>
                    <small class="text-success fw-semibold">
                        <i class="ti ti-user"></i> Standard
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear usuario -->
    @can('users.create')
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">
                        <i class="bx bx-user-plus me-2"></i>
                        Crear Nuevo Usuario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-mb-3">
                                <label for="name" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Ingrese el nombre completo" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="usuario@ejemplo.com" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-mb-3">
                                <label for="role" class="form-label">Rol</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Seleccionar rol...</option>
                                    @foreach(Spatie\Permission\Models\Role::all() as $role)
                                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i>
                            Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

</div>

@endsection

@push('vendor-scripts')
<script src="{{ asset('vuexy/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endpush
