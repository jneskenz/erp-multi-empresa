<div>
    <!-- Controles superiores -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-package me-2"></i>
                        Gestión de Artículos
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    @can('articulos.create')
                        <a href="{{ route('articulos.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>
                            Nuevo Artículo
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Filtros y búsqueda -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               class="form-control" 
                               placeholder="Buscar artículos...">
                        @if($search)
                            <button class="btn btn-outline-secondary" 
                                    wire:click="clearSearch" 
                                    type="button">
                                <i class="ti ti-x"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <select wire:model.live="filtros.estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select wire:model.live="filtros.inventariable" class="form-select">
                        <option value="">Inventariables</option>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" 
                               type="checkbox" 
                               wire:model.live="filtros.bajo_stock"
                               id="bajo_stock">
                        <label class="form-check-label" for="bajo_stock">
                            Bajo stock
                        </label>
                    </div>
                </div>

                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
            </div>

            <!-- Acciones en lote -->
            @if(count($selected) > 0)
                <div class="alert alert-info">
                    <div class="row align-items-center">
                        <div class="col">
                            <i class="ti ti-info-circle me-2"></i>
                            {{ count($selected) }} artículo(s) seleccionado(s)
                        </div>
                        <div class="col-auto">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-success" 
                                        wire:click="bulkToggleStatus(true)"
                                        wire:confirm="¿Activar los artículos seleccionados?">
                                    <i class="ti ti-check me-1"></i>
                                    Activar
                                </button>
                                <button class="btn btn-warning" 
                                        wire:click="bulkToggleStatus(false)"
                                        wire:confirm="¿Desactivar los artículos seleccionados?">
                                    <i class="ti ti-x me-1"></i>
                                    Desactivar
                                </button>
                                <button class="btn btn-danger" 
                                        wire:click="bulkDelete"
                                        wire:confirm="¿Eliminar los artículos seleccionados? Esta acción no se puede deshacer.">
                                    <i class="ti ti-trash me-1"></i>
                                    Eliminar
                                </button>
                                <button class="btn btn-secondary" wire:click="clearSelection">
                                    <i class="ti ti-x me-1"></i>
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover table-small table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr class="text-center align-middle">
                            <th width="30">
                                <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                            </th>
                            <th width="80">Imagen</th>
                            <th wire:click="sortBy('codigo')" class="{{ $this->getSortClass('codigo') }}">
                                Código
                                <i class="ti {{ $this->getSortIcon('codigo') }} ms-1"></i>
                            </th>
                            <th wire:click="sortBy('nombre')" class="{{ $this->getSortClass('nombre') }}">
                                Nombre
                                <i class="ti {{ $this->getSortIcon('nombre') }} ms-1"></i>
                            </th>
                            <th>Marca<hr class="my-0">Modelo</th>
                            <th>Stock</th>
                            <th wire:click="sortBy('precio_venta')" class="{{ $this->getSortClass('precio_venta') }}">
                                Precio Venta
                                <i class="ti {{ $this->getSortIcon('precio_venta') }} ms-1"></i>
                            </th>
                            <th>Estado</th>
                            <th><i class="ti ti-settings"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->articulos as $articulo)
                            <tr wire:key="articulo-{{ $articulo->id }}">
                                <td>
                                    <input type="checkbox" 
                                           wire:model.live="selected" 
                                           value="{{ $articulo->id }}" 
                                           class="form-check-input">
                                </td>
                                <td>
                                    @if($articulo->imagen)
                                        <img src="{{ Storage::url($articulo->imagen) }}" 
                                             alt="{{ $articulo->nombre }}" 
                                             class="rounded" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="ti ti-package text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-dark text-dark">{{ $articulo->codigo }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $articulo->nombre }}</strong>
                                        @if($articulo->descripcion)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($articulo->descripcion, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($articulo->marca || $articulo->modelo)
                                        <div>
                                            @if($articulo->marca)
                                                <span class="badge bg-label-info">{{ Str::limit($articulo->marca, 12) }}</span>
                                            @endif
                                            @if($articulo->modelo)
                                                <br><small class="text-muted">{{ $articulo->modelo }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-start">
                                    <div>
                                        <span class="badge bg-label-{{ $articulo->estado_stock == 'bajo' ? 'danger' : ($articulo->estado_stock == 'alto' ? 'warning' : 'success') }}">
                                            {{ $articulo->stock_actual }} {{ $articulo->unidad_medida }}
                                        </span>
                                        <hr class="my-1">
                                        <small>
                                            Min: {{ $articulo->stock_minimo }} <br> Max: {{ $articulo->stock_maximo }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <strong class="text-success">
                                        S/ {{ number_format($articulo->precio_venta, 2) }}
                                    </strong>
                                    @if($articulo->precio_costo > 0)
                                        <br>
                                        <small class="text-muted">
                                            Costo: S/ {{ number_format($articulo->precio_costo, 2) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge bg-label-{{ $articulo->estado ? 'success' : 'warning' }}">
                                            {{ $articulo->estado_texto }}
                                        </span>
                                        @if($articulo->inventariable)
                                            <span class="badge bg-label-info">Inventariable</span>
                                        @endif
                                        @if($articulo->vendible)
                                            <span class="badge bg-label-primary">Vendible</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-label-primary btn-icon rounded btn-sm dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('articulos.view')
                                                <li><a class="dropdown-item" href="{{ route('articulos.show', $articulo) }}"><i class="ti ti-list-search me-2"></i>Ver detalle</a></li>
                                            @endcan
                                            @can('articulos.edit')
                                                <li><a class="dropdown-item" href="{{ route('articulos.edit', $articulo) }}"><i class="ti ti-edit me-2"></i>Editar</a></li>
                                            @endcan
                                            <li>
                                                <hr class="dropdown-divider" />
                                            </li>
                                            @can('articulos.edit')
                                                <li>
                                                    <button class="btn btn-label-{{ $articulo->estado ? 'warning' : 'success' }} dropdown-item" 
                                                            wire:click="toggleStatus({{ $articulo->id }})"
                                                            title="{{ $articulo->estado ? 'Desactivar' : 'Activar' }}">
                                                        <i class="ti ti-{{ $articulo->estado ? 'eye-off' : 'check' }}"></i>
                                                    </button>
                                                </li>
                                            @endcan
                                            @can('articulos.delete')
                                            <li>
                                                @can('articulos.delete')
                                                    <a  href="javascript:void(0);"
                                                        class="dropdown-item btn btn-label-danger me-2" 
                                                        wire:click="deleteArticle({{ $articulo->id }})"
                                                        wire:confirm="¿Estás seguro de eliminar este artículo?"
                                                        title="Eliminar">
                                                        <i class="ti ti-trash"></i> Eliminar
                                                    </a>
                                                @endcan
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="ti ti-package display-4 text-muted"></i>
                                        <h6 class="mt-3">No se encontraron artículos</h6>
                                        <p class="text-muted">No hay artículos que coincidan con los criterios de búsqueda.</p>
                                        @if($search || array_filter($filtros))
                                            <button class="btn btn-sm btn-outline-primary" wire:click="clearAllFilters">
                                                <i class="ti ti-filter-off me-1"></i>
                                                Limpiar filtros
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($this->articulos->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando {{ $this->articulos->firstItem() }} a {{ $this->articulos->lastItem() }} 
                        de {{ $this->articulos->total() }} resultados
                    </div>
                    <div>
                        {{ $this->articulos->links('components.erp.table-pagination-custom') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Loading overlay -->
    {{-- <div wire:loading class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
         style="background: rgba(255,255,255,0.8); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div> --}}
</div>
