@extends('layouts.vuexy')

@section('title', 'Gestión de Roles - ERP Multisoft')

@php
    $dataBreadcrumb = [
        'title' => 'Gestión de Roles y Permisos',
        'description' => 'Gestiona y controla los roles y permisos',
        'icon' => 'ti ti-shield',
        'breadcrumbs' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Roles y permisos', 'url' => route('roles.index'), 'active' => true]
        ],
        'actions' => [
            // ['name' => 'Crear Rol', 'url' => route('roles.create'), 'icon' => 'ti ti-plus', 'permission' => 'roles.create'],
        ],
        'stats' => [
            ['name' => 'Total Roles', 'value' => Spatie\Permission\Models\Role::count(), 'icon' => 'ti ti-building', 'color' => 'bg-label-primary'],
            ['name' => 'Total Permisos Asignados', 'value' => $roles->where('estado', true)->count(), 'icon' => 'ti ti-circle-check', 'color' => 'bg-label-success'],
        ]
    ];

    $dataHeaderCard = [
        'title' => 'Lista de Roles y Permisos',
        'description' => 'Gestiona y controla roles y asigna permisos',
        'textColor' => 'text-primary',
        'icon' => 'ti ti-shield',
        'iconColor' => 'alert-primary',
        'actions' => [
            [
                'typeAction' => 'btnToggle', // btnIdEvent, btnLink, btnToggle, btnInfo
                'name' => 'Crear Rol',
                'icon' => 'ti ti-plus',
                'permission' => 'roles.create',
                'typeButton' => 'btn-primary',
                'idModal' => 'createRoleModal' // necesario si es btnToggle
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

                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="ti ti-shield me-2"></i>
                        Roles del Sistema
                    </h6>
                </div>

                <div class="card-body">
                    <!-- Lista de roles -->
                    <div class="row">
                        @foreach(Spatie\Permission\Models\Role::all() as $role)
                            <div class="col-lg-4 col-md-6 col-12 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div class="card-title mb-0">
                                                <h5 class="mb-0">
                                                    @if($role->name === 'admin')
                                                        <i class="ti ti-crown text-warning me-2"></i>
                                                    @elseif($role->name === 'manager')
                                                        <i class="ti ti-user-share text-info me-2"></i>
                                                    @else
                                                        <i class="ti ti-user text-success me-2"></i>
                                                    @endif
                                                    {{ ucfirst($role->name) }}
                                                </h5>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn p-0" type="button" id="roleCard{{ $role->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="ti ti-dots"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="roleCard{{ $role->id }}">
                                                    @can('roles.edit')
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="ti ti-edit me-1"></i> Editar
                                                        </a>
                                                    @endcan
                                                    @can('roles.delete')
                                                        @if($role->name !== 'admin')
                                                            <a class="dropdown-item text-danger" href="javascript:void(0);">
                                                                <i class="ti ti-trash me-1"></i> Eliminar
                                                            </a>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h6 class="d-flex align-items-center mb-2">
                                                        <span class="badge badge-center bg-label-secondary me-1">
                                                            <i class="ti ti-user text-muted"></i>
                                                        </span>
                                                        <span class="fw-normal">Usuarios</span>
                                                    </h6>
                                                    <h4 class="mb-0 ms-2">{{ App\Models\User::role($role->name)->count() }}</h4>
                                                </div>
                                                <div class="col-6">
                                                    <h6 class="d-flex align-items-center mb-2">
                                                        <span class="badge badge-center bg-label-info me-1">
                                                            <i class="ti ti-lock-open text-muted"></i>
                                                        </span>
                                                        <span class="fw-normal">Permisos</span>
                                                    </h6>
                                                    <h4 class="mb-0 ms-2">{{ $role->permissions->count() }}</h4>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Permisos del rol -->
                                        @if($role->permissions->count() > 0)
                                            <div class="mt-3">
                                                <h6 class="card-title">Permisos Asignados:</h6>
                                                <div class="demo-inline-spacing">
                                                    @foreach($role->permissions->take(6) as $permission)
                                                        <span class="badge bg-label-primary">{{ $permission->name }}</span>
                                                    @endforeach
                                                    @if($role->permissions->count() > 6)
                                                        <span class="badge bg-label-secondary">+{{ $role->permissions->count() - 6 }} más</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Descripción del rol -->
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                @if($role->name === 'admin')
                                                    Acceso completo al sistema. Control total sobre usuarios, configuraciones y módulos.
                                                @elseif($role->name === 'manager')
                                                    Gestión operativa del sistema. Acceso a reportes y administración de datos.
                                                @else
                                                    Acceso estándar del sistema. Funcionalidades básicas de consulta y operación.
                                                @endif
                                            </small>
                                        </div>

                                        @can('roles.edit')
                                            <div class="mt-3 pt-3 border-top">
                                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $role->id }}">
                                                    <i class="ti ti-edit me-1"></i>
                                                    Gestionar Permisos
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Tabla de permisos del sistema -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="ti ti-key me-2"></i>
                                        Permisos del Sistema
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Permiso</th>
                                                    <th>Admin</th>
                                                    <th>Manager</th>
                                                    <th>User</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(Spatie\Permission\Models\Permission::all() as $permission)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $permission->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ ucwords(str_replace('.', ' ', $permission->name)) }}</small>
                                                        </td>
                                                        @foreach(['admin', 'manager', 'user'] as $roleName)
                                                            @php
                                                                $role = Spatie\Permission\Models\Role::where('name', $roleName)->first();
                                                                $hasPermission = $role && $role->hasPermissionTo($permission);
                                                            @endphp
                                                            <td>
                                                                @if($hasPermission)
                                                                    <span class="badge bg-success">
                                                                        <i class="ti ti-check"></i> Sí
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-secondary">
                                                                        <i class="ti ti-x"></i> No
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear rol -->
@can('roles.create')
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRoleModalLabel">
                    <i class="ti ti-shield me-2"></i>
                    Crear Nuevo Rol
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="role_name" class="form-label">Nombre del Rol</label>
                            <input type="text" class="form-control" id="role_name" name="name" placeholder="Ej: editor, supervisor, etc." required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label class="form-label">Permisos del Rol</label>
                            <div class="row">
                                @foreach(Spatie\Permission\Models\Permission::all() as $permission)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ ucwords(str_replace('.', ' ', $permission->name)) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>
                        Crear Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
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

    // Seleccionar todos los permisos
    $('#selectAllPermissions').change(function() {
        $('input[name="permissions[]"]').prop('checked', this.checked);
    });
});
</script>
@endpush
