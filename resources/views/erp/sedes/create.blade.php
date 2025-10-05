@extends('layouts.vuexy')

@section('title', 'Crear Sede - ERP Multisoft')

@php
    $dataBreadcrumb = [
        'title' => 'Gestión de Sedes',
        'description' => 'Registra una nueva sede en el sistema',
        'icon' => 'ti ti-building-bank',
        'breadcrumbs' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Sedes', 'url' => route('sedes.index')],
            ['name' => 'Crear sede', 'url' => route('sedes.create'), 'active' => true],
        ],
        'actions' => [
            [
                'name' => 'Regresar',
                'url' => route('empresas.index'),
                'typeButton' => 'btn-label-dark',
                'icon' => 'ti ti-arrow-left',
                'permission' => 'empresas.view'
            ],
        ],
    ];

    $dataHeaderCard = [
        'title' => 'Formulario de registro',
        'description' => '',
        'textColor' => 'text-info',
        'icon' => 'ti ti-plus',
        'iconColor' => 'bg-label-info',
        'actions' => [],
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

                    {{-- <div class="card-header d-flex align-items-center">
                        <i class="ti ti-building-plus me-2 text-primary"></i>
                        <h5 class="mb-0">Formulario de registro para Sede</h5>
                    </div> --}}

                    <form action="{{ route('sedes.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bx bx-error-circle me-2"></i>
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
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
                                                    {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
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
                                        Selecciona la empresa a la que pertenecerá esta sede
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
                                           value="{{ old('nombre') }}" 
                                           placeholder="Ej: Sede Central, Sucursal Norte, etc."
                                           maxlength="25" 
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
                                           value="{{ old('codigo') }}" 
                                           placeholder="Ej: CENTRAL, NORTTE01, SEDE02, etc."
                                           maxlength="25" 
                                           required>
                                    @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" id="estado" name="estado" value="1" {{ old('estado', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="estado">Sede Activa</label>
                                    </div>
                                    <div class="form-text">La sede estará disponible para operaciones</div>
                                </div>
                                {{-- <div class="col-md-4">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado">
                                        <option value="1" {{ old('estado', '1') == '1' ? 'selected' : '' }}>
                                            <i class="ti ti-check"></i> Activo
                                        </option>
                                        <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>
                                            <i class="ti ti-x"></i> Inactivo
                                        </option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> --}}
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
                                              maxlength="255">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="char-count">0</span>/255 caracteres
                                    </div>
                                </div>
                            </div>

                            {{-- Información adicional --}}
                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <i class="ti ti-info-circle me-2 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Información importante:</h6>
                                        <ul class="mb-0 small">
                                            <li>El nombre de la sede debe ser único para cada empresa</li>
                                            <li>Las sedes inactivas no aparecerán en los procesos operativos</li>
                                            <li>Puedes modificar esta información posteriormente desde la opción "Editar"</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-between gap-2">
                                <a href="{{ route('sedes.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x me-1"></i>
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>
                                    Crear Sede
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Sedes existentes para la empresa seleccionada --}}
                <div class="card mt-4" id="existing-sedes" style="display: none;">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="ti ti-list-details me-1"></i>
                            Sedes existentes para esta empresa
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="sedes-list">
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </div>
                    </div>
                </div>
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
        updateCharCount(); // Inicializar contador
    }

    // Mostrar sedes existentes cuando se selecciona una empresa
    const empresaSelect = document.getElementById('empresa_id');
    const existingSedesCard = document.getElementById('existing-sedes');
    const sedesList = document.getElementById('sedes-list');
    
    if (empresaSelect) {
        empresaSelect.addEventListener('change', function() {
            const empresaId = this.value;
            
            if (empresaId) {
                // Realizar petición AJAX para obtener sedes de la empresa
                fetch(`/api/empresas/${empresaId}/sedes`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.sedes && data.sedes.length > 0) {
                            let sedesHtml = '<div class="row">';
                            data.sedes.forEach(sede => {
                                sedesHtml += `
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center p-2 border rounded">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-${sede.estado ? 'success' : 'danger'}">
                                                    ${sede.nombre.charAt(0)}
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="fw-medium">${sede.nombre}</small>
                                                <br>
                                                <small class="text-muted">
                                                    <span class="badge bg-${sede.estado ? 'success' : 'danger'} badge-sm">
                                                        ${sede.estado ? 'Activo' : 'Inactivo'}
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            sedesHtml += '</div>';
                            sedesList.innerHTML = sedesHtml;
                            existingSedesCard.style.display = 'block';
                        } else {
                            existingSedesCard.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        existingSedesCard.style.display = 'none';
                    });
            } else {
                existingSedesCard.style.display = 'none';
            }
        });
    }

    // Validación en tiempo real
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validar campos requeridos
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Campos requeridos',
                    text: 'Por favor completa todos los campos obligatorios',
                });
            }
        });
    }
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
</style>
@endpush