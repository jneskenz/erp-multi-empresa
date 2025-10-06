@extends('layouts.app-ws')

@section('title', 'Nueva Empresa - ERP Multisoft')


@php

    $breadcrumbs = [
        'title' => 'Gestión de Empresas',
        'description' => 'Administra las empresas del sistema',
        'icon' => 'ti ti-building',
        'items' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            [
                'name' => 'Empresas',
                'url' => route('grupo.empresas.index', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]),
            ],
            ['name' => 'Crear empresa', 'url' => '', 'active' => true],
        ],
    ];

@endphp


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <x-workspace.breadcrumbs :items="$breadcrumbs">
            <x-slot:extra>
                @can('ver_empresas')
                    <a href="{{ route('grupo.empresas.index', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]) }}"
                        class="btn btn-label-dark waves-effect">
                        <i class="ti ti-arrow-left me-2"></i>
                        Regresar
                    </a>
                @endcan
            </x-slot:extra>
        </x-workspace.breadcrumbs>

        <div class="row">
            <div class="col-md-8 mb-4">
                <div class="card">

                    <x-workspace.card-header title="Formulario de registro" description="" textColor="text-plus"
                        icon="ti ti-building" iconColor="bg-label-info">
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
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                <span class="alert-icon rounded"><i class="ti ti-x"></i></span>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">¡Error!</h6>
                                    <p class="mb-0">{{ session('error') }}</p>
                                </div>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-warning d-flex align-items-start mb-4" role="alert">
                                <span class="alert-icon rounded"><i class="ti ti-alert-triangle"></i></span>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading fw-bold mb-1">¡Atención!</h6>
                                    <p class="mb-2">Se encontraron los siguientes errores:</p>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form
                            action="{{ route('grupo.empresas.store', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]) }}"
                            method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                            @csrf

                            <!-- Información Básica -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="fw-bold">
                                        <i class="ti ti-building me-2"></i> Información principal
                                    </h6>
                                    <hr>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="ruc" class="form-label">
                                        RUC <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('ruc') is-invalid @enderror"
                                        id="ruc" name="ruc" value="{{ old('ruc') }}" placeholder="20123456789"
                                        maxlength="11" required>
                                    @error('ruc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Ingrese el RUC de 11 dígitos</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="codigo" class="form-label">
                                        Código <span class="text-muted">(Generado automáticamente)</span>
                                    </label>
                                    <input type="text" class="form-control" id="codigo" name="codigo"
                                        value="Se generará automáticamente: EMP####" placeholder="Ej: EMP0001" readonly
                                        disabled>
                                    <div class="form-text">El código se generará automáticamente al crear la empresa</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="razon_social" class="form-label">
                                        Razón Social <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('razon_social') is-invalid @enderror"
                                        id="razon_social" name="razon_social" value="{{ old('razon_social') }}"
                                        placeholder="Ingrese el razon_social de la empresa" required>
                                    @error('razon_social')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">
                                        Nombre Comercial <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control @error('nombre_comercial') is-invalid @enderror"
                                        id="nombre_comercial" name="nombre_comercial" value="{{ old('nombre_comercial') }}"
                                        placeholder="Ingrese el nombre de la empresa" required>
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
                                    <textarea class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion"
                                        rows="3" placeholder="Ingrese la dirección completa de la empresa" required>{{ old('direccion') }}</textarea>
                                    @error('direccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="representante_legal" class="form-label">Representante Legal</label>
                                    <input type="text"
                                        class="form-control @error('representante_legal') is-invalid @enderror"
                                        id="representante_legal" name="representante_legal"
                                        value="{{ old('representante_legal') }}"
                                        placeholder="Nombre del representante legal">
                                    @error('representante_legal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="grupo_empresa_id" class="form-label">Grupo Empresarial</label>
                                    <select class="form-select required @error('grupo_empresa_id') is-invalid @enderror"
                                        id="grupo_empresa_id" name="grupo_empresa_id">
                                        <option value="">Seleccionar grupo empresarial</option>
                                        <option value="{{ $grupoActual->id }}"
                                            {{ old('grupo_empresa_id') == $grupoActual->id ? 'selected' : '' }}>
                                            {{ $grupoActual->nombre }}
                                        </option>
                                    </select>
                                    @error('grupo_empresa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" id="activo" name="activo"
                                            value="1" {{ old('activo', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="activo">Empresa Activa</label>
                                    </div>
                                    <div class="form-text">La empresa estará disponible para operaciones</div>
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <div class="row mb-4 mt-4">
                                <div class="col-12">
                                    <h6 class="fw-bold">
                                        <i class="ti ti-phone me-2"></i> Información de Contacto
                                    </h6>
                                    <hr>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                        id="telefono" name="telefono" value="{{ old('telefono') }}"
                                        placeholder="(01) 123-4567">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}"
                                        placeholder="empresa@ejemplo.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Imagen Corporativa -->
                            <div class="row mb-4 mt-4">
                                <div class="col-12">
                                    <h6 class="fw-bold">
                                        <i class="ti ti-photo me-2"></i> Imagen Corporativa
                                    </h6>
                                    <hr>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="logo" class="form-label">
                                        Logo de la Empresa (SVG)
                                    </label>
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                        id="logo" name="logo" accept=".svg,image/svg+xml"
                                        onchange="previewLogo(event)">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Solo archivos SVG. Tamaño máximo: 2MB
                                    </div>

                                    <!-- Vista previa del logo -->
                                    <div id="logo-preview" class="mt-2 p-3 border rounded bg-light"
                                        style="display: none;">
                                        <div class="d-flex align-items-center">
                                            <img id="logo-preview-img" src="" alt="Vista previa"
                                                style="max-height: 60px; max-width: 150px;" class="me-3">
                                            <div>
                                                <small class="text-muted d-block">Vista previa del logo</small>
                                                <small class="badge bg-label-info">Nuevo</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="favicon" class="form-label">
                                        Favicon (ICO, PNG, SVG)
                                    </label>
                                    <input type="file" class="form-control @error('favicon') is-invalid @enderror"
                                        id="favicon" name="favicon"
                                        accept=".ico,.png,.svg,image/x-icon,image/png,image/svg+xml"
                                        onchange="previewFavicon(event)">
                                    @error('favicon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Formatos: ICO, PNG, SVG. Recomendado: 32x32px o 64x64px. Máx: 1MB
                                    </div>

                                    <!-- Vista previa del favicon -->
                                    <div id="favicon-preview" class="mt-2 p-3 border rounded bg-light"
                                        style="display: none;">
                                        <div class="d-flex align-items-center">
                                            <img id="favicon-preview-img" src="" alt="Vista previa"
                                                style="width: 32px; height: 32px;" class="me-3">
                                            <div>
                                                <small class="text-muted d-block">Vista previa del favicon</small>
                                                <small class="badge bg-label-info">Nuevo</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('grupo.empresas.index', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]) }}"
                                            class="btn btn-outline-secondary">
                                            <i class="ti ti-x me-1"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-check me-1"></i> Crear Empresa
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">

                    <x-workspace.card-header title="Instrucciones"
                        description="Sigue estos pasos para registrar una nueva empresa" textColor="text-plus"
                        icon="ti ti-info-circle" iconColor="bg-label-info">
                    </x-workspace.card-header>

                    <div class="card-body">
                        <div class="demo-inline-spacing mt-4">
                            <ul class="list-group">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                    <span>Completa todos los campos obligatorios marcados con <span class="text-danger">*</span>.</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                    Asegúrate de ingresar un RUC válido de 11 dígitos.
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                    Puedes subir un logo en formato SVG y un favicon en ICO, PNG o SVG.
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                    Revisa toda la información antes de enviar el formulario.
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="ti ti-square-check text-secondary ti-md me-3"></i>
                                    Después de crear la empresa, podrás gestionar sus usuarios y configuraciones.
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

            // Auto-dismiss alertas después de 5 segundos
            const alerts = document.querySelectorAll('.alert .btn-close');
            alerts.forEach(function(closeBtn) {
            setTimeout(function() {
                closeBtn.click();
            }, 8000); // 8 segundos para dar tiempo a leer
            });

            // Validación del formulario
            const form = document.querySelector('.needs-validation');
            if (form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();

                    // Scroll al primer campo inválido
                    const invalidInputs = form.querySelectorAll(':invalid');
                    if (invalidInputs.length > 0) {
                        invalidInputs[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        invalidInputs[0].focus();
                    }
                }
                form.classList.add('was-validated');
            });
            }

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
