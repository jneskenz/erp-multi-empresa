@extends('layouts.vuexy')

@section('title', 'Editar Sede - ERP Multisoft')

@php

    $dataBreadcrumb = [
        'title' => 'Gestión de Sedes',
        'description' => '',
        'icon' => 'ti ti-building-bank',
        'breadcrumbs' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Sedes', 'url' => route('sedes.index')],
            ['name' => 'Editar sede', 'url' => 'javascript:void(0)', 'active' => true],
        ],
        'actions' => [
            [
                'name' => 'Regresar',
                'url' => route('sedes.index'),
                'typeButton' => 'btn-label-dark',
                'icon' => 'ti ti-arrow-left',
                'permission' => 'sedes.view'
            ],

        ],
    ];

    $dataHeaderCard = [
        'title' => 'Editando la sede',
        'description' => $sede->nombre,
        'textColor' => 'text-warning',
        'icon' => 'ti ti-edit',
        'iconColor' => 'bg-label-warning',        
        'actions' => [
            [
                'typeAction' => 'btnInfo',
                'name' => $sede->estado == 1 ? 'ACTIVO' : 'SUSPENDIDO',
                'url' => '#',
                'icon' => $sede->estado == 1 ? 'ti ti-check' : 'ti ti-x',
                'permission' => null,
                'typeButton' => $sede->estado == 1 ? 'btn-label-success' : 'btn-label-danger',
            ],
            [
                'typeAction' => 'btnLink',
                'name' => 'Ver detalle',
                'url' => route('sedes.show', $sede),
                'typeButton' => 'btn-info',
                'icon' => 'ti ti-list-search',
                'permission' => 'sedes.view'
            ],
            
        ],
    ];

@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb Component -->
        @include('layouts.vuexy.breadcrumb', $dataBreadcrumb)

        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="card">


                    @include('layouts.vuexy.header-card', $dataHeaderCard)

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bx bx-error-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('sedes.update', $sede) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="card-body">
                            {{-- Empresa --}}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="empresa_id" class="form-label required">Empresa</label>
                                    <select class="form-select @error('empresa_id') is-invalid @enderror" 
                                            id="empresa_id" 
                                            name="empresa_id" 
                                            required>
                                        <option value="">Seleccione una empresa</option>
                                        @foreach($empresas as $empresa)
                                            <option value="{{ $empresa->id }}" 
                                                    {{ old('empresa_id', $sede->empresa_id) == $empresa->id ? 'selected' : '' }}>
                                                {{ $empresa->nombre_comercial ?? $empresa->razon_social }} 
                                                ({{ $empresa->numerodocumento }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('empresa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Empresa actual: <strong>{{ $sede->empresa->nombre_comercial ?? $sede->empresa->razon_social }}</strong>
                                    </div>
                                </div>
                            </div>

                            {{-- Nombre de la Sede --}}
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="nombre" class="form-label required">Nombre de la Sede</label>
                                    <input type="text" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="{{ old('nombre', $sede->nombre) }}" 
                                           placeholder="Ej: Sede Central, Sucursal Norte, etc."
                                           maxlength="255" 
                                           required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="codigo" class="form-label required">Código corto</label>
                                    <input type="text" 
                                           class="form-control @error('codigo') is-invalid @enderror" 
                                           id="codigo" 
                                           name="codigo" 
                                           value="{{ old('codigo', $sede->codigo) }}" 
                                           placeholder="Ej: CENTRAL, NORTTE01, SEDE02, etc."
                                           maxlength="255" 
                                           required>
                                    @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select @error('estado') is-invalid @enderror" 
                                            id="estado" 
                                            name="estado">
                                        <option value="1" {{ old('estado', $sede->estado) == '1' ? 'selected' : '' }}>
                                            Activo
                                        </option>
                                        <option value="0" {{ old('estado', $sede->estado) == '0' ? 'selected' : '' }}>
                                            Inactivo
                                        </option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Descripción --}}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                              id="descripcion" 
                                              name="descripcion" 
                                              rows="4" 
                                              placeholder="Describe las características, ubicación o propósito de esta sede..."
                                              maxlength="1000">{{ old('descripcion', $sede->descripcion) }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="char-count">{{ strlen($sede->descripcion ?? '') }}</span>/1000 caracteres
                                    </div>
                                </div>
                            </div>

                            {{-- Información de auditoría --}}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card bg-light border-0">
                                        <div class="card-body p-3">
                                            <h6 class="mb-2">
                                                <i class="ti ti-info-circle me-1"></i>
                                                Información de registro
                                            </h6>
                                            <div class="row text-sm">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <strong>Creado:</strong> 
                                                        {{ $sede->created_at->format('d/m/Y \a \l\a\s H:i') }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <strong>Última modificación:</strong> 
                                                        {{ $sede->updated_at->format('d/m/Y \a \l\a\s H:i') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Alerta de cambios --}}
                            @if(session('warning'))
                                <div class="alert alert-warning">
                                    <div class="d-flex">
                                        <i class="ti ti-alert-triangle me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Ten en cuenta:</h6>
                                            <p class="mb-0 small">{{ session('warning') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <i class="ti ti-info-circle me-2 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Importante:</h6>
                                        <ul class="mb-0 small">
                                            <li>El nombre de la sede debe ser único para cada empresa</li>
                                            <li>Cambiar el estado a "Inactivo" puede afectar procesos operativos</li>
                                            <li>Los cambios se reflejarán inmediatamente en el sistema</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('sedes.index') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-x me-1"></i>
                                        Cancelar
                                    </a>
                                </div>
                                <div class="d-flex gap-2">
                                    @can('sedes.edit')
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-check me-1"></i>
                                            Actualizar Sede
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Otras sedes de la misma empresa --}}
                @if($otrasSedesEmpresa->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="ti ti-list-details me-1"></i>
                                Otras sedes de {{ $sede->empresa->nombre_comercial ?? $sede->empresa->razon_social }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($otrasSedesEmpresa as $otraSede)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center p-2 border rounded">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-{{ $otraSede->estado ? 'success' : 'danger' }}">
                                                    {{ substr($otraSede->nombre, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="fw-medium">{{ $otraSede->nombre }}</small>
                                                <br>
                                                <small class="text-muted">
                                                    <span class="badge bg-{{ $otraSede->estado ? 'success' : 'danger' }} badge-sm">
                                                        {{ $otraSede->estado ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                </small>
                                            </div>
                                            <div>
                                                @can('sedes.view')
                                                    <a href="{{ route('sedes.show', $otraSede) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Ver detalles">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador de caracteres para descripción
    const descripcionTextarea = document.getElementById('descripcion');
    const charCount = document.getElementById('char-count');
    
    if (descripcionTextarea && charCount) {
        function updateCharCount() {
            charCount.textContent = descripcionTextarea.value.length;
        }
        
        descripcionTextarea.addEventListener('input', updateCharCount);
    }

    // Confirmación de cambios importantes
    const empresaSelect = document.getElementById('empresa_id');
    const estadoSelect = document.getElementById('estado');
    const originalEmpresa = {{ $sede->empresa_id }};
    const originalEstado = {{ $sede->estado ? 1 : 0 }};
    
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let warnings = [];
            
            // Verificar cambio de empresa
            if (empresaSelect.value != originalEmpresa) {
                warnings.push('Cambiarás la empresa de esta sede');
            }
            
            // Verificar cambio de estado a inactivo
            if (estadoSelect.value == 0 && originalEstado == 1) {
                warnings.push('La sede pasará a estado inactivo');
            }
            
            if (warnings.length > 0) {
                e.preventDefault();
                
                Swal.fire({
                    title: '¿Confirmar cambios?',
                    html: `
                        <div class="text-start">
                            <p class="mb-2">Se realizarán los siguientes cambios:</p>
                            <ul class="mb-0">
                                ${warnings.map(warning => `<li>${warning}</li>`).join('')}
                            </ul>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, actualizar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#696cff',
                    cancelButtonColor: '#8592a3'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });
    }

    // Validación en tiempo real
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.required:after {
    content: ' *';
    color: #e74c3c;
}

.avatar-xs {
    width: 24px;
    height: 24px;
}

.avatar-xs .avatar-initial {
    font-size: 10px;
    line-height: 24px;
}

.border:hover {
    border-color: #696cff !important;
    transition: border-color 0.2s ease;
}

.text-sm {
    font-size: 0.875rem;
}
</style>
@endpush