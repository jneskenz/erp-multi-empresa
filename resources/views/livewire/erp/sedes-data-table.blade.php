<div class="">

    {{-- Filtros y búsqueda --}}
    <div class="row mb-4">


        <div class="col-md-3 my-1 my-md-0">
            <select class="form-select" wire:model.live="empresaFilter">
                <option value="">Todas las empresas</option>
                @foreach ($empresas as $empresa)
                    <option value="{{ $empresa->id }}">
                        {{ $empresa->nombre_comercial ?? $empresa->razon_social }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 my-1 my-md-0">
            <select class="form-select" wire:model.live="estadoFilter">
                <option value="">Todos los estados</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>

        <div class="col-md-2 my-1 my-md-0">
            <select class="form-select" wire:model.live="perPage">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <div class="col-md-4 my-1 my-md-0">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" class="form-control" placeholder="Buscar sedes..."
                    wire:model.live.debounce.300ms="search">
            </div>
        </div>

        <div class="col-md-1">
            <div class="d-flex gap-1">
                <button class="btn btn-outline-secondary" wire:click="clearFilters" data-bs-toggle="tooltip"
                    title="Limpiar filtros">
                    <i class="ti ti-filter-x"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla de sedes con loading --}}
    <div class="position-relative">
        {{-- Loading overlay solo para operaciones que requieren tiempo --}}
        {{-- <div wire:loading.delay.long wire:target="search,empresaFilter,estadoFilter,perPage,clearFilters"
                 class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75 rounded" 
                 style="z-index: 10; min-height: 400px;">
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <span class="text-muted">Cargando datos...</span>
                </div>
            </div> --}}

        {{-- Tabla --}}
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-dark text-center align-middle">
                    <tr>
                        <th wire:click="sortBy('nombre')" style="cursor: pointer;">
                            NOMBRE SEDE
                            @if ($sortField === 'nombre')
                                <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </th>
                        <th>EMPRESA</th>
                        <th>DESCRIPCIÓN</th>
                        <th wire:click="sortBy('estado')" style="cursor: pointer;">
                            ESTADO
                            @if ($sortField === 'estado')
                                <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                            FECHA CREACIÓN
                            @if ($sortField === 'created_at')
                                <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </th>
                        <th class="text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sedes as $sede)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ substr($sede->nombre, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>{{ $sede->nombre }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <small
                                        class="fw-medium">{{ $sede->empresa->nombre_comercial ?? $sede->empresa->razon_social }}</small><br>
                                    <small class="text-muted">{{ $sede->empresa->numerodocumento }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="text-truncate d-block" style="max-width: 200px;"
                                    title="{{ $sede->descripcion }}">
                                    {{ $sede->descripcion ?? 'Sin descripción' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $sede->estado ? 'success' : 'danger' }}">
                                    <i class="ti ti-{{ $sede->estado ? 'check' : 'x' }} me-1"></i>
                                    {{ $sede->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $sede->created_at->format('d/m/Y') }}<br>
                                    {{ $sede->created_at->format('H:i') }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button"
                                        class="btn btn-label-primary btn-icon rounded btn-sm dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @can('sedes.view')
                                            <li><a class="dropdown-item" href="{{ route('sedes.show', $sede) }}"><i class="ti ti-list-search me-2"></i>Ver detalles</a></li>
                                        @endcan
                                        @can('sedes.edit')
                                            <li><a class="dropdown-item" href="{{ route('sedes.edit', $sede) }}"><i class="ti ti-edit me-2"></i>Editar</a></li>
                                        @endcan
                                        <li>
                                            <hr class="dropdown-divider" />
                                        </li>
                                        @can('sedes.delete')
                                        <li>
                                            <form method="POST" action="{{ route('sedes.destroy', $sede) }}"
                                                onsubmit="return confirm('¿Estás seguro de eliminar esta sede?')"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="ti ti-trash me-2"></i>
                                                    Eliminar
                                                </button>
                                            </form>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="ti ti-building-warehouse" style="font-size: 4rem; color: #8b8fa3;"></i>
                                    <h5 class="mt-3">No hay sedes registradas</h5>
                                    <p class="text-muted mb-3">
                                        @if ($search || $empresaFilter || $estadoFilter !== '')
                                            No se encontraron sedes con los filtros aplicados.
                                        @else
                                            Comienza creando tu primera sede.
                                        @endif
                                    </p>
                                    @if (!$search && !$empresaFilter && $estadoFilter === '')
                                        @can('sedes.create')
                                            <a href="{{ route('sedes.create') }}" class="btn btn-primary">
                                                <i class="ti ti-plus me-1"></i>
                                                Crear primera sede
                                            </a>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Información y Paginación mejorada --}}
    <div class="row mt-4 align-items-center">
        <div class="col-sm-12 col-md-6 d-flex align-items-center mb-3 mb-md-0">
            <div class="text-muted">
                @if ($sedes->total() > 0)
                    <i class="ti ti-filter-check me-1"></i>
                    Mostrando <strong class="text-primary">{{ $sedes->firstItem() }}</strong> a
                    <strong class="text-primary">{{ $sedes->lastItem() }}</strong> de
                    <strong class="text-primary">{{ $sedes->total() }}</strong>
                    {{ Str::plural('resultado', $sedes->total()) }}

                    @if ($search || $empresaFilter || $estadoFilter !== '')
                        <span class="badge bg-label-info ms-2">
                            <i class="ti ti-filter me-1"></i>
                            Filtrado
                        </span>
                    @endif
                @else
                    <span class="text-muted">
                        <i class="ti ti-info-circle me-1"></i>
                        No se encontraron resultados
                    </span>
                @endif
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            @if ($sedes->hasPages())
                <div class="d-flex justify-content-end">
                    {{ $sedes->links('custom-pagination') }}
                </div>
            @endif
        </div>
    </div>

</div>
