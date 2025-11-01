@extends('layouts.app-adm')

@section('title', 'Gestión de Grupos Empresariales')

@push('styles')
<style>
.table th a {
    color: #6c757d !important;
    font-weight: 600;
    transition: color 0.2s ease;
}

.table th a:hover {
    color: #495057 !important;
}

.pagination {
    margin-bottom: 0;
}

.form-label {
    font-weight: 600;
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.search-info {
    background-color: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 0.375rem;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
}

.pagination-info {
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
@endpush

@php
    $breadcrumbs = [
        'title' => 'Grupos Empresariales',
        'description' => 'Administración de grupos empresariales del sistema',
        'icon' => 'ti ti-building-bank',
        'items' => [
            ['name' => 'Configuración del Sistema', 'url' => 'javascript:void(0)'],
            ['name' => 'Grupos Empresariales', 'url' => 'javascript:void(0)', 'active' => true],
        ],
    ];
@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <x-erp.breadcrumbs :items="$breadcrumbs">
            <x-slot:acciones>
                @can('grupo_empresarial.create')
                    <a href="{{ route('admin.grupos.create') }}" class="btn btn-primary waves-effect">
                        <i class="ti ti-plus me-2"></i>
                        Nuevo Grupo
                    </a>
                @endcan
            </x-slot:acciones>
        </x-erp.breadcrumbs>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ti ti-building-bank me-2"></i>
                    Lista de Grupos Empresariales
                </h5>
                <span class="badge bg-primary">{{ $grupos->total() }} grupos total</span>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-check me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-x me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Filtros y Búsqueda -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <!-- Indicador de filtros activos -->
                        @if(request()->hasAny(['search', 'estado', 'sort', 'direction']))
                            <div class="search-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="ti ti-filter me-2"></i>
                                        <strong>Filtros activos:</strong>
                                        @if(request('search'))
                                            <span class="badge bg-primary ms-2">Búsqueda: "{{ request('search') }}"</span>
                                        @endif
                                        @if(request('estado') !== null)
                                            <span class="badge bg-info ms-2">Estado: {{ request('estado') == '1' ? 'Activo' : 'Inactivo' }}</span>
                                        @endif
                                        @if(request('sort') && request('sort') != 'nombre')
                                            <span class="badge bg-secondary ms-2">Orden: {{ ucfirst(request('sort')) }} {{ request('direction') == 'desc' ? '↓' : '↑' }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('admin.grupos.index') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-x me-1"></i>Limpiar filtros
                                    </a>
                                </div>
                            </div>
                        @endif

                        <form method="GET" action="{{ route('admin.grupos.index') }}" class="row g-3">
                            <div class="col-md-2">
                                {{-- <label for="per_page" class="form-label">Por página</label> --}}
                                <select class="form-select" id="per_page" name="per_page">
                                    <option value="5" {{ request('per_page') == '5' ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ request('per_page', '10') == '10' ? 'selected' : '' }}>10</option>
                                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                {{-- <label for="search" class="form-label">Buscar</label> --}}
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" 
                                           placeholder="Buscar por nombre, código, descripción...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                {{-- <label for="estado" class="form-label">Estado</label> --}}
                                <select class="form-select" id="estado" name="estado">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                            {{-- <div class="col-md-2">
                                <label for="sort" class="form-label">Ordenar por</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="nombre" {{ request('sort') == 'nombre' ? 'selected' : '' }}>Nombre</option>
                                    <option value="codigo" {{ request('sort') == 'codigo' ? 'selected' : '' }}>Código</option>
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Fecha Creación</option>
                                    <option value="pais_origen" {{ request('sort') == 'pais_origen' ? 'selected' : '' }}>País</option>
                                </select>
                            </div> --}}
                            {{-- <div class="col-md-2">
                                <label for="direction" class="form-label">Dirección</label>
                                <select class="form-select" id="direction" name="direction">
                                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                                </select>
                            </div> --}}
                            
                            <div class="col-2 text-center">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-search me-2"></i>Buscar
                                    </button>
                                    <a href="{{ route('admin.grupos.index') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-refresh me-2"></i>Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @if ($grupos->count() > 0)
                    <!-- Información de paginación superior -->
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>UUID</th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'codigo', 'direction' => request('sort') == 'codigo' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-dark">
                                            CÓDIGO
                                            @if(request('sort') == 'codigo')
                                                <i class="ti ti-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'nombre', 'direction' => request('sort') == 'nombre' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-dark">
                                            GRUPO EMPRESARIAL
                                            @if(request('sort') == 'nombre' || !request('sort'))
                                                <i class="ti ti-arrow-{{ (request('direction') == 'asc' || !request('direction')) ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'pais_origen', 'direction' => request('sort') == 'pais_origen' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-decoration-none text-dark">
                                            PAÍS ORIGEN
                                            @if(request('sort') == 'pais_origen')
                                                <i class="ti ti-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>EMPRESAS</th>
                                    <th>ESTADO</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($grupos as $grupo)
                                    <tr>
                                        <td>
                                            <span
                                                class="badge bg-label-success">{{ Str::limit($grupo->user_uuid, 10) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-info">{{ $grupo->codigo }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $grupo->nombre }}</strong>
                                                @if ($grupo->descripcion)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($grupo->descripcion, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            {{ $grupo->pais_origen ?? '-' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-label-secondary">{{ $grupo->empresas_count }}
                                                empresa(s)</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-{{ $grupo->estado ? 'success' : 'danger' }}">
                                                {{ $grupo->estado ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button"
                                                    class="btn btn-label-primary btn-icon btn-sm rounded dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @can('empresas.view')
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.grupos.show', $grupo) }}">
                                                                <i class="ti ti-list-search me-2"></i> Ver detalles
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('empresas.edit')
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.grupos.edit', $grupo) }}">
                                                                <i class="ti ti-edit me-2"></i> Editar
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    <li>
                                                        <hr class="dropdown-divider" />
                                                    </li>
                                                    @can('admin.grupos.delete')
                                                        <li>
                                                            <form
                                                                action="{{ route('admin.grupos.toggle-status', $grupo) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="ti ti-{{ $grupo->estado ? 'x' : 'check' }} me-2"></i>
                                                                    {{ $grupo->estado ? 'Desactivar' : 'Activar' }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endcan
                                                    @can('admin.grupos.delete')
                                                        <li>
                                                            @if ($grupo->empresas_count == 0)
                                                                <form
                                                                    action="{{ route('admin.grupos.destroy', $grupo) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger"
                                                                        onclick="return confirm('¿Está seguro de eliminar este grupo empresarial?')">
                                                                        <i class="ti ti-trash me-2"></i>Eliminar
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="pagination-info">
                            Mostrando {{ $grupos->firstItem() }} - {{ $grupos->lastItem() }} de {{ $grupos->total() }} resultados
                        </div>
                        <div>
                            {{ $grupos->links('components.erp.table-pagination') }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        @if(request('search') || request('estado') !== null)
                            <i class="ti ti-search-off display-4 text-muted"></i>
                            <h4 class="mt-3">No se encontraron resultados</h4>
                            <p class="text-muted">Intente modificar los criterios de búsqueda</p>
                            <a href="{{ route('admin.grupos.index') }}" class="btn btn-outline-primary">
                                <i class="ti ti-refresh me-2"></i>Ver todos los grupos
                            </a>
                        @else
                            <i class="ti ti-building-bank display-4 text-muted"></i>
                            <h4 class="mt-3">No hay grupos empresariales registrados</h4>
                            <p class="text-muted">Comience creando su primer grupo empresarial</p>
                            @can('grupo_empresarial.create')
                                <a href="{{ route('admin.grupos.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-2"></i>Crear Grupo Empresarial
                                </a>
                            @endcan
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit cuando cambian los filtros de select
    const autoSubmitSelects = ['estado', 'sort', 'direction', 'per_page'];
    
    autoSubmitSelects.forEach(function(selectId) {
        const select = document.getElementById(selectId);
        if (select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });

    // Búsqueda con delay para evitar muchas peticiones
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500); // 500ms delay
        });
    }

    // Limpiar filtros
    const clearButton = document.querySelector('a[href*="grupos.index"]:not([href*="?"])');
    if (clearButton) {
        clearButton.addEventListener('click', function(e) {
            e.preventDefault();
            // Limpiar todos los campos del formulario
            const form = document.querySelector('form');
            if (form) {
                form.reset();
                // Redirect to clean URL
                window.location.href = this.href;
            }
        });
    }
});
</script>
@endpush
