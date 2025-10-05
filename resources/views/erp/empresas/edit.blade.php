@extends('layouts.vuexy')

@section('title', 'Editar Empresa - ERP Multisoft')

@php
    $dataBreadcrumb = [
        'title' => 'Gestión de Empresas',
        'description' => 'Administra las empresas del sistema',
        'icon' => 'ti ti-building',
        'breadcrumbs' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Empresas', 'url' => route('empresas.index')],
            ['name' => 'Editar empresa', 'url' => '', 'active' => true]
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
        'title' => 'Editando la empresa',
        'description' => $empresa->nombre_comercial ?? $empresa->razon_social,
        'textColor' => 'text-warning',
        'icon' => 'ti ti-edit',
        'iconColor' => 'bg-warning',
        'actions' => [
            [
                'typeAction' => 'btnLink',
                'typeButton' => 'btn-info',
                'name' => 'Ver detalle',
                'url' => route('empresas.show', $empresa),
                'icon' => 'ti ti-list-search',
                'permission' => 'empresas.view'
            ],
        ],
    ];

@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    @include('layouts.vuexy.breadcrumb', $dataBreadcrumb)

    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="alert alert-warning d-flex mb-4" role="alert">
                        <span class="alert-icon rounded"><i class="ti ti-edit"></i></span>
                        <div>
                            <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Editando: {{ $empresa->nombre_comercial ?? $empresa->razon_social }}</h6>
                            <p class="mb-0">Modifique los campos necesarios y guarde los cambios. Todos los cambios serán registrados en el historial.</p>
                        </div>
                    </div>
                    <div></div>
                </div> --}}

                @include('layouts.vuexy.header-card', $dataHeaderCard)

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

                    <form action="{{ route('empresas.update', $empresa) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Información Básica -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold">
                                    <i class="ti ti-building me-2"></i> Información Básica
                                </h6>
                                <hr>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="numerodocumento" class="form-label">
                                    RUC <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('numerodocumento') is-invalid @enderror"
                                    id="numerodocumento" name="numerodocumento"
                                    value="{{ old('numerodocumento', $empresa->numerodocumento) }}"
                                    placeholder="20123456789"
                                    maxlength="11"
                                    required>
                                @error('numerodocumento')
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
                                        id="estado" name="estado"
                                        value="1"
                                        {{ old('estado', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="estado">Empresa Activa</label>
                                </div>
                                <div class="form-text" title="No puede inactivar o suspender la empresa por este formulario">
                                    Esta opción solo está permitido para activar la empresa.
                                </div>
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
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input type="email"
                                    class="form-control @error('correo') is-invalid @enderror"
                                    id="correo" name="correo"
                                    value="{{ old('correo', $empresa->correo) }}"
                                    placeholder="empresa@ejemplo.com">
                                @error('correo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary">
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
    const rucInput = document.getElementById('numerodocumento');
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
</script>
@endpush
