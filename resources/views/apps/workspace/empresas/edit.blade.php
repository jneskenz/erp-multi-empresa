@extends('layouts.app-ws')

@section('title', 'Editar Empresa - ERP Multisoft')

@php
    $breadcrumbs = [
        'title' => 'Gestión de Empresas',
        'description' => 'Administra las empresas del sistema',
        'icon' => 'ti ti-building',
        'items' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Empresas', 'url' => route('grupo.empresas.index', ['grupo' => $grupoActual->slug ?? request()->route('grupo')])],
            ['name' => $empresa->nombre_comercial ?? $empresa->razon_social, 'url' => 'javascript:void(0);'],
            ['name' => 'Editar', 'url' => 'javascript:void(0);']
        ],
    ];

@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <x-workspace.breadcrumbs :items="$breadcrumbs">
        <x-slot:extra>
            @can('ver_empresas')
            <a href="{{ route('grupo.empresas.index', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]) }}" class="btn btn-label-dark waves-effect">
                <i class="ti ti-arrow-left me-2"></i>
                Regresar
            </a>
            @endcan
        </x-slot:extra>
    </x-workspace.breadcrumbs>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card">

                <x-workspace.card-header 
                    title="Editando la empresa" 
                    description="{{ $empresa->nombre_comercial ?? $empresa->razon_social }}"
                    textColor="text-warning"
                    icon="ti ti-edit"
                    iconColor="bg-warning"
                    estado="{{ $empresa->estado ? '1' : '0' }}"
                >
                    @can('empresas.view')
                        <a href="{{ route('grupo.empresas.show', ['grupo' => $grupoActual->slug ?? request()->route('grupo'), 'empresa' => $empresa->id]) }}" class="btn btn-info waves-effect">
                            <i class="ti ti-list-search me-2"></i>
                            Ver detalle
                        </a>                        
                    @endcan
                </x-workspace.card-header>

                <div class="card-body">
                    {{-- Mensajes de alerta --}}
                    @if (session('success'))
                        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                            <span class="alert-icon rounded"><i class="ti ti-check"></i></span>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">¡Éxito!</h6>
                                <p class="mb-0">{{ session('success') }}</p>
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                            <span class="alert-icon rounded"><i class="ti ti-x"></i></span>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">¡Error!</h6>
                                <p class="mb-0">{{ session('error') }}</p>
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-warning d-flex align-items-start mb-4" role="alert">
                            <span class="alert-icon rounded"><i class="ti ti-alert-square-rounded"></i></span>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading fw-bold mb-1">¡Atención!</h6>
                                <p class="mb-2">Se encontraron los siguientes errores:</p>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('grupo.empresas.update', ['grupo' => $grupoActual->slug ?? request()->route('grupo'), 'empresa' => $empresa->id]) }}" method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Información Básica -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold mb-0">
                                    <i class="ti ti-building me-2"></i> Información Básica
                                </h6>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ruc" class="form-label">
                                    RUC <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                    class="form-control @error('ruc') is-invalid @enderror"
                                    id="ruc" name="ruc"
                                    value="{{ old('ruc', $empresa->ruc) }}"
                                    placeholder="20123456789"
                                    maxlength="11"
                                    required>
                                @error('ruc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Ingrese el RUC de 11 dígitos</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="razon_social" class="form-label">
                                    Razón Social <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('razon_social') is-invalid @enderror"
                                    id="razon_social" name="razon_social"
                                    value="{{ old('razon_social', $empresa->razon_social) }}"
                                    placeholder="Ingrese la razón social"
                                    required>
                                @error('razon_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="nombre_comercial" class="form-label">
                                    Nombre Comercial
                                </label>
                                <input type="text"
                                    class="form-control @error('nombre_comercial') is-invalid @enderror"
                                    id="nombre_comercial" name="nombre_comercial"
                                    value="{{ old('nombre_comercial', $empresa->nombre_comercial) }}"
                                    placeholder="Ingrese el nombre comercial">
                                @error('nombre_comercial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="direccion" class="form-label">
                                    Dirección <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror"
                                    id="direccion" name="direccion"
                                    rows="3"
                                    placeholder="Ingrese la dirección completa de la empresa"
                                    required>{{ old('direccion', $empresa->direccion) }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="representante_legal" class="form-label">Representante Legal</label>
                                <input type="text"
                                    class="form-control @error('representante_legal') is-invalid @enderror"
                                    id="representante_legal" name="representante_legal"
                                    value="{{ old('direccion', $empresa->direccion) }}"
                                    placeholder="Nombre del representante legal">
                                @error('representante_legal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        id="activo" name="activo"
                                        value="1"
                                        {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Empresa Activa</label>
                                </div>
                                <div class="form-text" title="No puede inactivar o suspender la empresa por este formulario">
                                    Esta opción solo está permitido para activar la empresa.
                                </div>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="fw-bold mb-0">
                                    <i class="ti ti-phone me-2"></i> Información de Contacto
                                </h6>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text"
                                    class="form-control @error('telefono') is-invalid @enderror"
                                    id="telefono" name="telefono"
                                    value="{{ old('telefono', $empresa->telefono) }}"
                                    placeholder="Ingrese el teléfono">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email"
                                    value="{{ old('email', $empresa->email) }}"
                                    placeholder="empresa@ejemplo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Imagen Corporativa -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="fw-bold mb-0">
                                    <i class="ti ti-photo me-2"></i> Imagen Corporativa
                                </h6>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="logo" class="form-label">
                                    Logo de la Empresa (SVG)
                                </label>
                                
                                @if($empresa->logo)
                                    <div class="mb-3 p-3 border rounded bg-light">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ Storage::url($empresa->logo) }}" 
                                                     alt="Logo actual" 
                                                     style="max-height: 60px; max-width: 150px;"
                                                     class="me-3">
                                                <div>
                                                    <small class="text-muted d-block">Logo actual</small>
                                                    <small class="badge bg-label-success">Activo</small>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="remove_logo" class="form-check-label cursor-pointer">
                                                    <input type="checkbox" 
                                                           class="form-check-input" 
                                                           id="remove_logo" 
                                                           name="remove_logo"
                                                           value="1">
                                                    <small>Eliminar</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <input type="file"
                                    class="form-control @error('logo') is-invalid @enderror"
                                    id="logo" 
                                    name="logo"
                                    accept=".svg,image/svg+xml"
                                    onchange="previewLogo(event)">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Solo archivos SVG. Tamaño máximo: 2MB
                                </div>
                                
                                <!-- Vista previa del nuevo logo -->
                                <div id="logo-preview" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <img id="logo-preview-img" 
                                             src="" 
                                             alt="Vista previa" 
                                             style="max-height: 60px; max-width: 150px;"
                                             class="me-3">
                                        <div>
                                            <small class="text-muted d-block">Nuevo logo</small>
                                            <small class="badge bg-label-info">Vista previa</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="favicon" class="form-label">
                                    Favicon (ICO, PNG, SVG)
                                </label>
                                
                                @if($empresa->favicon)
                                    <div class="mb-3 p-3 border rounded bg-light">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ Storage::url($empresa->favicon) }}" 
                                                     alt="Favicon actual" 
                                                     style="width: 32px; height: 32px;"
                                                     class="me-3">
                                                <div>
                                                    <small class="text-muted d-block">Favicon actual</small>
                                                    <small class="badge bg-label-success">Activo</small>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="remove_favicon" class="form-check-label cursor-pointer">
                                                    <input type="checkbox" 
                                                           class="form-check-input" 
                                                           id="remove_favicon" 
                                                           name="remove_favicon"
                                                           value="1">
                                                    <small>Eliminar</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <input type="file"
                                    class="form-control @error('favicon') is-invalid @enderror"
                                    id="favicon" 
                                    name="favicon"
                                    accept=".ico,.png,.svg,image/x-icon,image/png,image/svg+xml"
                                    onchange="previewFavicon(event)">
                                @error('favicon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Formatos: ICO, PNG, SVG. Recomendado: 32x32px o 64x64px. Máx: 1MB
                                </div>
                                
                                <!-- Vista previa del nuevo favicon -->
                                <div id="favicon-preview" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <img id="favicon-preview-img" 
                                             src="" 
                                             alt="Vista previa" 
                                             style="width: 32px; height: 32px;"
                                             class="me-3">
                                        <div>
                                            <small class="text-muted d-block">Nuevo favicon</small>
                                            <small class="badge bg-label-info">Vista previa</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('grupo.empresas.index', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]) }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-x me-1"></i> Cancelar
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-check me-1"></i> Actualizar Empresa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><span class="ti ti-help"></span> Ayuda Rápida</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        Desde este formulario puedes editar la información básica y de contacto de la empresa seleccionada.
                    </p>
                    <div class="demo-inline-spacing mt-4">
                        <ul class="list-group">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                <p class="mb-0">El campo <strong>RUC</strong> debe contener exactamente 11 dígitos numéricos.</p>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                <p class="mb-0">Si la <strong>empresa</strong> ya <strong>contiene facturaciones</strong>, no se puede editar el RUC y la Razón Social, se debe crear una nueva empresa.</p>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                <p class="mb-0">La <strong>Razón Social</strong> es obligatoria y debe reflejar el nombre legal de la empresa.</p>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                <p class="mb-0">El <strong>Nombre Comercial</strong> es opcional y puede ser diferente a la razón social.</p>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                <p class="mb-0">La <strong>Dirección</strong> debe ser lo más completa posible para facilitar la ubicación de la empresa.</p>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                <p class="mb-0">El campo de <strong>Representante Legal</strong> es opcional pero recomendado para identificar a la persona responsable.</p>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                <p class="mb-0">El estado "Empresa Activa" solo permite activar la empresa. Para inactivar o suspender, contacta con el administrador del sistema.</p>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                <p class="mb-0">Los campos de <strong>Teléfono</strong> y <strong>Correo Electrónico</strong> son opcionales pero útiles para mantener una comunicación efectiva.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // Validación de formulario
    const form = document.querySelector('.needs-validation');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }

    // Auto-dismiss alertas después de 8 segundos
    const alerts = document.querySelectorAll('.alert .btn-close');
    alerts.forEach(function(closeBtn) {
        setTimeout(function() {
            closeBtn.click();
        }, 8000);
    });

    // Formato de RUC
    const rucInput = document.getElementById('ruc');
    if (rucInput) {
        rucInput.addEventListener('input', function(e) {
            // Solo permitir números
            this.value = this.value.replace(/\D/g, '');

            // Limitar a 11 dígitos
            if (this.value.length > 11) {
                this.value = this.value.substr(0, 11);
            }

            // Validación en tiempo real
            if (this.value.length > 0 && this.value.length < 11) {
                this.setCustomValidity('El RUC debe tener exactamente 11 dígitos');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Validación de email en tiempo real
    const emailInput = document.getElementById('correo');
    if (emailInput) {
        emailInput.addEventListener('blur', function(e) {
            if (this.value && !this.checkValidity()) {
                this.setCustomValidity('Ingrese un correo electrónico válido');
            } else {
                this.setCustomValidity('');
            }
        });
    }

});
    });

});

// Vista previa del logo
function previewLogo(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('logo-preview');
    const previewImg = document.getElementById('logo-preview-img');
    
    if (file) {
        // Validar tipo de archivo
        if (!file.type.includes('svg')) {
            alert('Por favor, selecciona un archivo SVG válido');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validar tamaño (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('El archivo es demasiado grande. El tamaño máximo es 2MB');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

// Vista previa del favicon
function previewFavicon(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('favicon-preview');
    const previewImg = document.getElementById('favicon-preview-img');
    
    if (file) {
        // Validar tipo de archivo
        const validTypes = ['image/x-icon', 'image/png', 'image/svg+xml'];
        if (!validTypes.includes(file.type)) {
            alert('Por favor, selecciona un archivo ICO, PNG o SVG válido');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validar tamaño (1MB)
        if (file.size > 1 * 1024 * 1024) {
            alert('El archivo es demasiado grande. El tamaño máximo es 1MB');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endpush
