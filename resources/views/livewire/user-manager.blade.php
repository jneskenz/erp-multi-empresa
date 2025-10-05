<div class="card-datatable table-responsive">
    <!-- Filtros superiores con estilo Vuexy -->
    <div class="card-header flex-column flex-md-row border-bottom">
        <div class="head-label text-center">
            {{-- <h5 class="card-title mb-0"> <i class="ti ti-users me-2"></i> Gestión de Usuarios </h5> --}}
        </div>
        <div class="dt-action-buttons text-end pt-3 pt-md-0">
            <div class="dt-buttons row g-2">
                <div class="col-sm-12 col-md-6">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar usuarios...">
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <select wire:model.live="selectedRole" class="form-select">
                        <option value="">🔍 Filtrar por rol...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">
                                @if($role->name === 'admin') 👑 @elseif($role->name === 'manager') 🎯 @else 👤 @endif
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes de estado con estilo Vuexy -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible d-flex" role="alert">
            <span class="alert-icon rounded"><i class="ti ti-check"></i></span>
            <div>
                <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">¡Éxito!</h6>
                <p class="mb-0">{{ session('success') }}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible d-flex" role="alert">
            <span class="alert-icon rounded"><i class="ti ti-x"></i></span>
            <div>
                <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Error</h6>
                <p class="mb-0">{{ session('error') }}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabla de usuarios estilo Vuexy -->
    <table class="datatables-users table border-top">
        <thead>
            <tr>
                <th class="text-center">ID</th>
                <th><i class="ti ti-user me-2"></i>USUARIO</th>
                <th><i class="ti ti-envelope me-2"></i>CORREO</th>
                <th class="text-center">ROL</th>
                <th class="text-center">REGISTRO</th>
                <th class="text-center">ESTADO</th>
                @can('users.edit')
                    <th class="text-center">ACCIONES</th>
                @endcan
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            @forelse($users as $user)
                <tr>
                    <td class="text-center">
                        <span class="badge bg-label-secondary">#{{ $user->id }}</span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-start align-items-center user-name">
                            <div class="avatar-wrapper">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $user->id % 3 === 0 ? 'primary' : ($user->id % 2 === 0 ? 'success' : 'info') }}">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="emp_name text-truncate fw-semibold">{{ $user->name }}</span>
                                <small class="emp_post text-muted">
                                    Miembro desde {{ $user->created_at->format('M Y') }}
                                </small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($user->email_verified_at)
                            <span class="badge bg-label-success ms-1">
                                <i class="ti ti-shield-check"></i>
                            </span>
                        @else
                            <span class="badge bg-label-warning ms-1">
                                <i class="ti ti-shield-x"></i>
                            </span>
                        @endif
                        <span class="text-muted">{{ $user->email }}</span>
                    </td>
                    <td class="text-center">
                        @forelse($user->roles as $role)
                            @php
                                $badgeClass = match($role->name) {
                                    'admin' => 'bg-label-warning',
                                    'manager' => 'bg-label-info',
                                    'user' => 'bg-label-primary',
                                    default => 'bg-label-secondary'
                                };
                                $icon = match($role->name) {
                                    'admin' => 'ti-crown',
                                    'manager' => 'ti-user-share',
                                    'user' => 'ti-user',
                                    default => 'ti-user'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} me-1">
                                <i class="ti {{ $icon }} me-1"></i>
                                {{ ucfirst($role->name) }}
                            </span>
                        @empty
                            <span class="badge bg-label-secondary">
                                <i class="ti ti-user-x me-1"></i>
                                Sin rol
                            </span>
                        @endforelse
                    </td>
                    <td class="text-center">
                        <small class="text-muted">{{ $user->created_at->format('d/m/Y') }}</small>
                        <br>
                        <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-label-success">
                            <i class="ti ti-circle-check me-1"></i>
                            Activo
                        </span>
                    </td>
                    <td class="text-center">
                        {{-- @can('users.edit') --}}
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <span class="badge bg-label-primary px-2">
                                    <i class="ti ti-user-cog"></i>
                                </span>
                            </button>
                            <div class="dropdown-menu">
                                <h6 class="dropdown-header">Gestionar Usuario</h6>
                                <a class="dropdown-item" href="javascript:void(0);">
                                    <i class="ti ti-clipboard-list me-1"></i> Ver Perfil
                                </a>
                                <a class="dropdown-item" href="javascript:void(0);">
                                    <i class="ti ti-edit me-1"></i> Editar Info
                                </a>
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Asignar Rol</h6>
                                @foreach($roles as $role)
                                    <a class="dropdown-item"
                                        href="#"
                                        wire:click.prevent="assignRole({{ $user->id }}, '{{ $role->name }}')">
                                        @php
                                            $icon = match($role->name) {
                                                'admin' => 'ti-crown',
                                                'manager' => 'ti-user-share',
                                                'user' => 'ti-user',
                                                default => 'ti-user'
                                            };
                                        @endphp
                                        <i class="ti {{ $icon }} me-1"></i>
                                        {{ ucfirst($role->name) }}
                                        @if($user->hasRole($role->name))
                                            <span class="badge bg-success ms-2">✓</span>
                                        @endif
                                    </a>
                                @endforeach

                                @if($user->id !== auth()->id())
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="javascript:void(0);"
                                        wire:confirm="¿Estás seguro de eliminar este usuario?">
                                        <i class="ti ti-trash me-1"></i> Eliminar Usuario
                                    </a>
                                @endif
                            </div>
                        </div>
                        {{-- @endcan --}}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center">
                            <img src="{{ asset('vuexy/img/illustrations/page-misc-error.png') }}"
                                 alt="No usuarios" width="120" class="mb-3">
                            <h6 class="mb-1">No se encontraron usuarios</h6>
                            <p class="text-muted mb-0">Intenta ajustar tu búsqueda o filtros para encontrar usuarios.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginación personalizada -->
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div>
            <small class="text-muted">
                <i class="ti ti-info-circle me-1"></i>
                Mostrando {{ $users->count() }} de {{ $users->total() }} usuarios
            </small>
        </div>
        <div>
            @if($users->hasPages())
                <nav aria-label="Paginación de usuarios">
                    {{ $users->links('pagination::bootstrap-4') }}
                </nav>
            @endif
        </div>
    </div>
</div>
