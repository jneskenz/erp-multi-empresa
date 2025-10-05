@extends('layouts.vuexy')

@section('title', 'Empresas - ' . $grupo->nombre)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('grupo.dashboard', ['grupo' => $grupo->slug]) }}">
                            {{ $grupo->nombre }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Empresas</li>
                </ol>
            </nav>
            <h4 class="mb-0">
                <i class="ti ti-building me-2"></i>Gestión de Empresas
            </h4>
        </div>
        <div>
            @if($grupo->empresas()->count() < $grupo->max_empresas)
                <a href="{{ route('grupo.empresas.create', ['grupo' => $grupo->slug]) }}" 
                   class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>Nueva Empresa
                </a>
            @else
                <button class="btn btn-secondary" disabled title="Límite de empresas alcanzado">
                    <i class="ti ti-lock me-1"></i>Límite Alcanzado ({{ $grupo->max_empresas }})
                </button>
            @endif
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('grupo.empresas.index', ['grupo' => $grupo->slug]) }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Nombre, RUC..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select name="activo" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ti ti-search me-1"></i>Buscar
                        </button>
                        <a href="{{ route('grupo.empresas.index', ['grupo' => $grupo->slug]) }}" 
                           class="btn btn-outline-secondary">
                            <i class="ti ti-x"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista de empresas --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                Lista de Empresas 
                <span class="badge bg-label-primary">{{ $empresas->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>RUC</th>
                            <th>Email</th>
                            <th>Usuarios</th>
                            <th>Módulos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empresas as $empresa)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $empresa->nombre }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $empresa->slug }}</small>
                                </div>
                            </td>
                            <td>{{ $empresa->ruc ?? 'N/A' }}</td>
                            <td>{{ $empresa->email }}</td>
                            <td>
                                <span class="badge bg-label-info">
                                    {{ $empresa->usuarios_count }}
                                </span>
                            </td>
                            <td>
                                @if($empresa->modulos_activos)
                                    @foreach($empresa->modulos_activos as $modulo)
                                        <span class="badge bg-label-primary">{{ $modulo }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Sin módulos</span>
                                @endif
                            </td>
                            <td>
                                @if($empresa->activo)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-danger">Inactiva</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" 
                                            data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" 
                                               href="{{ route('empresa.dashboard', ['grupo' => $grupo->slug, 'empresa' => $empresa->slug]) }}">
                                                <i class="ti ti-dashboard me-2"></i>Ver Dashboard
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" 
                                               href="{{ route('grupo.empresas.show', ['grupo' => $grupo->slug, 'empresa' => $empresa->id]) }}">
                                                <i class="ti ti-eye me-2"></i>Ver Detalles
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" 
                                               href="{{ route('grupo.empresas.edit', ['grupo' => $grupo->slug, 'empresa' => $empresa->id]) }}">
                                                <i class="ti ti-edit me-2"></i>Editar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @if($empresa->activo)
                                        <li>
                                            <form action="{{ route('grupo.empresas.desactivar', ['grupo' => $grupo->slug, 'empresa' => $empresa->id]) }}" 
                                                  method="POST" onsubmit="return confirm('¿Desactivar esta empresa?')">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-warning">
                                                    <i class="ti ti-ban me-2"></i>Desactivar
                                                </button>
                                            </form>
                                        </li>
                                        @else
                                        <li>
                                            <form action="{{ route('grupo.empresas.activar', ['grupo' => $grupo->slug, 'empresa' => $empresa->id]) }}" 
                                                  method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-success">
                                                    <i class="ti ti-check me-2"></i>Activar
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        <li>
                                            <form action="{{ route('grupo.empresas.destroy', ['grupo' => $grupo->slug, 'empresa' => $empresa->id]) }}" 
                                                  method="POST" onsubmit="return confirm('¿Eliminar esta empresa? Esta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="ti ti-trash me-2"></i>Eliminar
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="ti ti-building mb-2" style="font-size: 3rem; color: #ddd;"></i>
                                <p class="mb-2 text-muted">No hay empresas registradas</p>
                                <a href="{{ route('grupo.empresas.create', ['grupo' => $grupo->slug]) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus me-1"></i>Crear Primera Empresa
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($empresas->hasPages())
            <div class="mt-4">
                {{ $empresas->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
