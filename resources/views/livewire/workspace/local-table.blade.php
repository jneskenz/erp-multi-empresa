<div>
    {{-- Filtros y controles --}}
    <div class="row g-3 mb-4">
        <div class="col-md-1">
            <label for="perPage" class="form-label">Mostrar</label>
            <select class="form-select" id="perPage" wire:model.live="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search" class="form-label">Buscar</label>
            <input type="text" class="form-control" id="search" wire:model.live.debounce.300ms="search"
                placeholder="Buscar por descripción, código, dirección...">
        </div>
        <div class="col-md-3">
            <label for="sedeFilter" class="form-label">Sede</label>
            <select class="form-select" id="sedeFilter" wire:model.live="sedeFilter">
                <option value="">Todas las sedes</option>
                @foreach ($sedes as $sede)
                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="estadoFilter" class="form-label">Estado</label>
            <select class="form-select" id="estadoFilter" wire:model.live="estadoFilter">
                <option value="">Todos</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>
        
        <div class="col-md-2">
            <label for="estadoFilter" class="form-label w-100">Acciones</label>
            <div class="d-flex gap-1  text-center align-content-between">               
                <button class="btn btn-outline-secondary" wire:click="clearFilters" data-bs-toggle="tooltip" title="Limpiar filtros">
                    <i class="ti ti-filter-x"></i>
                </button>
                <button type="button" class="btn btn-outline-primary" wire:click="$refresh" data-bs-toggle="tooltip" title="Actualizar">
                    <i class="ti ti-refresh"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla de locales --}}
    <div class="row">
        <div class="col-md-12">
            @if ($locales->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th wire:click="sortBy('codigo')" style="cursor: pointer;">
                                    Código
                                    @if ($sortField === 'codigo')
                                        <i class="bx bx-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-arrow-alt"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('descripcion')" style="cursor: pointer;">
                                    Local
                                    @if ($sortField === 'descripcion')
                                        <i class="bx bx-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-arrow-alt"></i>
                                    @endif
                                </th>
                                <th>Sede</th>
                                <th>Dirección</th>
                                <th>Contacto</th>
                                <th wire:click="sortBy('estado')" style="cursor: pointer;">
                                    Estado
                                    @if ($sortField === 'estado')
                                        <i class="bx bx-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-arrow-alt"></i>
                                    @endif
                                </th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($locales as $local)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $local->codigo }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $local->descripcion }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $local->sede->nombre ?? 'Sin sede' }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($local->direccion, 40) }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            @if ($local->telefono)
                                                <small><i
                                                        class="bx bx-phone me-1"></i>{{ $local->telefono }}</small><br>
                                            @endif
                                            @if ($local->correo)
                                                <small><i class="bx bx-envelope me-1"></i>{{ $local->correo }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm {{ $local->estado ? 'btn-label-success' : 'btn-label-secondary' }} px-2" wire:click="toggleEstado({{ $local->id }})" title="{{ $local->estado ? 'Desactivar' : 'Activar' }}">
                                            <i class="ti ti-{{ $local->estado ? 'check' : 'x' }}  me-1"></i>
                                            {{ $local->estado ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button"
                                                class="btn btn-label-primary btn-icon rounded btn-sm dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @can('locales.view')
                                                    <li><a class="dropdown-item" href="{{ route('locales.show', $local) }}"><i class="ti ti-list-search me-2"></i>Ver detalles</a></li>
                                                @endcan
                                                @can('locales.edit')
                                                    <li><a class="dropdown-item" href="{{ route('locales.edit', $local) }}"><i class="ti ti-edit me-2"></i>Editar</a></li>
                                                @endcan
                                                @can('locales.delete')
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger"
                                                        wire:click="confirmDelete({{ $local->id }})">
                                                        <i class="bx bx-trash me-2"></i>Eliminar
                                                    </button>
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

                {{-- Paginación --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            <i class="ti ti-filter-check me-1"></i>
                            Mostrando de {{ $locales->firstItem() ?? 0 }} a {{ $locales->lastItem() ?? 0 }}
                            de {{ $locales->total() }} resultados
                        </small>
                    </div>
                    <div>
                        {{ $locales->links('components.erp.table-pagination-custom') }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bx bx-building-house display-1 text-muted"></i>
                    <h5 class="mt-3">No hay locales registrados</h5>
                    <p class="text-muted">Comienza creando tu primer local</p>
                    <a href="{{ route('locales.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>
                        Crear Local
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal de confirmación de eliminación --}}
    @if ($showDeleteModal)
        <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="bx bx-error-circle display-1 text-warning"></i>
                            <h5 class="mt-3">¿Estás seguro?</h5>
                            <p class="text-muted">
                                Vas a eliminar el local: <br>
                                <strong>{{ $localToDelete->descripcion ?? '' }}</strong>
                            </p>
                            <p class="text-danger">
                                <small>Esta acción no se puede deshacer.</small>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDelete">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="deleteLocal">
                            <i class="bx bx-trash me-1"></i>
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Loading indicator --}}
    <div wire:loading class="position-fixed top-50 start-50 translate-middle">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
</div>
