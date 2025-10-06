<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <!-- Información Básica -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            Información Básica
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="codigo" class="form-label">Código</label>
                                <div class="input-group">
                                    <input type="text" 
                                           wire:model="codigo" 
                                           class="form-control @error('codigo') is-invalid @enderror"
                                           placeholder="Automático si se deja vacío"
                                           @if($modo === 'view') readonly @endif>
                                    @if($modo !== 'view')
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                wire:click="generarCodigo"
                                                title="Generar código automático">
                                            <i class="ti ti-refresh"></i>
                                        </button>
                                    @endif
                                </div>
                                @error('codigo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="nombre" class="form-label">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       wire:model="nombre" 
                                       class="form-control @error('nombre') is-invalid @enderror"
                                       placeholder="Nombre del artículo"
                                       @if($modo === 'view') readonly @endif
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea wire:model="descripcion" 
                                          class="form-control @error('descripcion') is-invalid @enderror"
                                          rows="3"
                                          placeholder="Descripción detallada del artículo"
                                          @if($modo === 'view') readonly @endif></textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" 
                                       wire:model="marca" 
                                       class="form-control @error('marca') is-invalid @enderror"
                                       placeholder="Marca del artículo"
                                       @if($modo === 'view') readonly @endif>
                                @error('marca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" 
                                       wire:model="modelo" 
                                       class="form-control @error('modelo') is-invalid @enderror"
                                       placeholder="Modelo del artículo"
                                       @if($modo === 'view') readonly @endif>
                                @error('modelo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="unidad_medida" class="form-label">
                                    Unidad de Medida <span class="text-danger">*</span>
                                </label>
                                @if($modo === 'view')
                                    <input type="text" 
                                           value="{{ $unidad_medida }}" 
                                           class="form-control" 
                                           readonly>
                                @else
                                    <select wire:model="unidad_medida" 
                                            class="form-select @error('unidad_medida') is-invalid @enderror"
                                            required>
                                        <option value="UND">Unidad</option>
                                        <option value="KG">Kilogramo</option>
                                        <option value="LT">Litro</option>
                                        <option value="MT">Metro</option>
                                        <option value="M2">Metro Cuadrado</option>
                                        <option value="M3">Metro Cúbico</option>
                                        <option value="PAQ">Paquete</option>
                                        <option value="CAJ">Caja</option>
                                        <option value="DOC">Docena</option>
                                        <option value="LIC">Licencia</option>
                                    </select>
                                @endif
                                @error('unidad_medida')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ubicacion" class="form-label">Ubicación</label>
                                <input type="text" 
                                       wire:model="ubicacion" 
                                       class="form-control @error('ubicacion') is-invalid @enderror"
                                       placeholder="Ej: Almacén A - Estante 1"
                                       @if($modo === 'view') readonly @endif>
                                @error('ubicacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="imagen" class="form-label">Imagen</label>
                                @if($modo === 'view')
                                    @if($imagen_actual)
                                        <div class="mt-2">
                                            <img src="{{ Storage::url($imagen_actual) }}" 
                                                 alt="Imagen del artículo" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 150px;">
                                        </div>
                                    @else
                                        <p class="text-muted">Sin imagen</p>
                                    @endif
                                @else
                                    <input type="file" 
                                           wire:model="imagen" 
                                           class="form-control @error('imagen') is-invalid @enderror"
                                           accept="image/*">
                                    @if($mostrar_imagen_actual && $imagen_actual)
                                        <div class="mt-2">
                                            <small class="text-muted">Imagen actual:</small>
                                            <br>
                                            <img src="{{ Storage::url($imagen_actual) }}" 
                                                 alt="Imagen actual" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 100px;">
                                        </div>
                                    @endif
                                    @if($imagen)
                                        <div class="mt-2">
                                            <small class="text-success">Nueva imagen cargada</small>
                                        </div>
                                    @endif
                                @endif
                                @error('imagen')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Especificaciones -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-list-details me-2"></i>
                            Especificaciones Técnicas
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($modo !== 'view')
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <input type="text" 
                                           wire:model="nueva_especificacion_clave" 
                                           class="form-control" 
                                           placeholder="Característica (ej: Color)">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" 
                                           wire:model="nueva_especificacion_valor" 
                                           class="form-control" 
                                           placeholder="Valor (ej: Azul)">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" 
                                            class="btn btn-primary w-100" 
                                            wire:click="agregarEspecificacion">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                </div>
                            </div>
                        @endif

                        @if(!empty($especificaciones))
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Característica</th>
                                            <th>Valor</th>
                                            @if($modo !== 'view')
                                                <th width="50">Acción</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($especificaciones as $clave => $valor)
                                            <tr>
                                                <td><strong>{{ $clave }}</strong></td>
                                                <td>{{ $valor }}</td>
                                                @if($modo !== 'view')
                                                    <td>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                wire:click="eliminarEspecificacion('{{ $clave }}')"
                                                                title="Eliminar">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">
                                <i class="ti ti-info-circle me-2"></i>
                                No se han agregado especificaciones técnicas
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="col-lg-4">
                <!-- Precios y Stock -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-currency-dollar me-2"></i>
                            Precios y Stock
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="precio_costo" class="form-label">
                                    Precio de Costo <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" 
                                           wire:model="precio_costo" 
                                           class="form-control @error('precio_costo') is-invalid @enderror"
                                           step="0.01" 
                                           min="0"
                                           @if($modo === 'view') readonly @endif
                                           required>
                                </div>
                                @error('precio_costo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="precio_venta" class="form-label">
                                    Precio de Venta <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" 
                                           wire:model="precio_venta" 
                                           class="form-control @error('precio_venta') is-invalid @enderror"
                                           step="0.01" 
                                           min="0"
                                           @if($modo === 'view') readonly @endif
                                           required>
                                </div>
                                @error('precio_venta')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if($precio_costo > 0 && $precio_venta > 0)
                            <div class="alert alert-info">
                                <small>
                                    <strong>Margen de ganancia:</strong> 
                                    {{ $this->calcularMargen() }}%
                                </small>
                            </div>
                        @endif

                        <hr>

                        <div class="row mb-3">
                            <div class="col-4">
                                <label for="stock_minimo" class="form-label">Stock Mín.</label>
                                <input type="number" 
                                       wire:model="stock_minimo" 
                                       class="form-control @error('stock_minimo') is-invalid @enderror"
                                       min="0"
                                       @if($modo === 'view') readonly @endif
                                       required>
                                @error('stock_minimo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-4">
                                <label for="stock_actual" class="form-label">Stock Actual</label>
                                <input type="number" 
                                       wire:model="stock_actual" 
                                       class="form-control @error('stock_actual') is-invalid @enderror"
                                       min="0"
                                       @if($modo === 'view') readonly @endif
                                       required>
                                @error('stock_actual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-4">
                                <label for="stock_maximo" class="form-label">Stock Máx.</label>
                                <input type="number" 
                                       wire:model="stock_maximo" 
                                       class="form-control @error('stock_maximo') is-invalid @enderror"
                                       min="0"
                                       @if($modo === 'view') readonly @endif
                                       required>
                                @error('stock_maximo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if($stock_actual > 0 && $stock_minimo > 0)
                            @php
                                $estado_stock = $stock_actual <= $stock_minimo ? 'bajo' : ($stock_actual >= $stock_maximo ? 'alto' : 'normal');
                                $color_stock = $estado_stock == 'bajo' ? 'danger' : ($estado_stock == 'alto' ? 'warning' : 'success');
                            @endphp
                            <div class="alert alert-{{ $color_stock }}">
                                <small>
                                    <strong>Estado del stock:</strong> 
                                    @if($estado_stock == 'bajo')
                                        ⚠️ Stock bajo
                                    @elseif($estado_stock == 'alto')
                                        📈 Stock alto
                                    @else
                                        ✅ Stock normal
                                    @endif
                                </small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Configuraciones -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-settings me-2"></i>
                            Configuraciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   wire:model="estado"
                                   id="estado"
                                   @if($modo === 'view') disabled @endif>
                            <label class="form-check-label" for="estado">
                                Artículo Activo
                            </label>
                            <div class="form-text">El artículo estará disponible en el sistema</div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   wire:model="inventariable"
                                   id="inventariable"
                                   @if($modo === 'view') disabled @endif>
                            <label class="form-check-label" for="inventariable">
                                Inventariable
                            </label>
                            <div class="form-text">Se lleva control de inventario</div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   wire:model="vendible"
                                   id="vendible"
                                   @if($modo === 'view') disabled @endif>
                            <label class="form-check-label" for="vendible">
                                Vendible
                            </label>
                            <div class="form-text">Puede ser vendido a clientes</div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   wire:model="comprable"
                                   id="comprable"
                                   @if($modo === 'view') disabled @endif>
                            <label class="form-check-label" for="comprable">
                                Comprable
                            </label>
                            <div class="form-text">Puede ser comprado a proveedores</div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                @if($modo !== 'view')
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="submit" 
                                    class="btn btn-primary btn-lg w-100 mb-2"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="ti ti-device-floppy me-2"></i>
                                    {{ $modo === 'create' ? 'Crear Artículo' : 'Actualizar Artículo' }}
                                </span>
                                <span wire:loading>
                                    <i class="ti ti-loader-2 me-2"></i>
                                    Guardando...
                                </span>
                            </button>
                            
                            <a href="{{ route('articulos.index') }}" 
                               class="btn btn-outline-secondary w-100">
                                <i class="ti ti-arrow-left me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>

    <!-- Loading overlay -->
    {{-- <div wire:loading.delay class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
         style="background: rgba(255,255,255,0.8); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div> --}}
</div>
