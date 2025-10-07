@extends('layouts.app')

@section('title', 'Seleccionar Contexto')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-building text-primary"></i>
                    Selecciona tu Empresa
                </h1>
                <p class="text-muted lead">
                    Elige la empresa y local donde deseas trabajar
                </p>
            </div>

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Formulario de selección -->
            <form id="contexto-form" action="{{ route('contexto.cambiar') }}" method="POST">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                <!-- Selección de Empresa -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-building me-2"></i>
                            Empresas Disponibles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($empresas as $empresa)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 empresa-card @if($empresaActual == $empresa->id) border-primary @endif" 
                                         style="cursor: pointer;"
                                         onclick="seleccionarEmpresa({{ $empresa->id }})">
                                        <div class="card-body">
                                            <!-- Logo o Icono -->
                                            <div class="text-center mb-3">
                                                @if($empresa->logo_url)
                                                    <img src="{{ $empresa->logo_url }}" 
                                                         alt="{{ $empresa->nombre }}" 
                                                         class="img-fluid rounded"
                                                         style="max-height: 80px;">
                                                @else
                                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                         style="width: 80px; height: 80px;">
                                                        <i class="fas fa-building fa-2x text-primary"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Información -->
                                            <h6 class="card-title text-center mb-2">
                                                {{ $empresa->nombre }}
                                            </h6>
                                            <p class="text-muted text-center small mb-2">
                                                RUC: {{ $empresa->ruc }}
                                            </p>

                                            <!-- Badge si es actual -->
                                            @if($empresaActual == $empresa->id)
                                                <div class="text-center">
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-check me-1"></i>
                                                        Actual
                                                    </span>
                                                </div>
                                            @endif

                                            <!-- Radio button oculto -->
                                            <input type="radio" 
                                                   name="empresa_id" 
                                                   value="{{ $empresa->id }}" 
                                                   id="empresa_{{ $empresa->id }}"
                                                   class="d-none"
                                                   @if($empresaActual == $empresa->id) checked @endif
                                                   required>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('empresa_id')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Selección de Local (se carga dinámicamente) -->
                <div class="card shadow-sm mb-4" id="locales-card" style="display: none;">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Seleccionar Local (Opcional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="locales-loading" class="text-center py-3" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="text-muted mt-2">Cargando locales...</p>
                        </div>

                        <div id="locales-container" class="row g-3">
                            <!-- Se cargan dinámicamente con JavaScript -->
                        </div>

                        <div id="sin-locales" class="alert alert-info" style="display: none;">
                            <i class="fas fa-info-circle me-2"></i>
                            Esta empresa no tiene locales configurados. Puedes continuar sin seleccionar local.
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver al Dashboard
                    </a>

                    <button type="submit" class="btn btn-primary btn-lg" id="btn-confirmar">
                        <i class="fas fa-check me-2"></i>
                        Confirmar Selección
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('styles')
<style>
    .empresa-card {
        transition: all 0.3s ease;
        border: 2px solid #dee2e6;
    }

    .empresa-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        border-color: var(--bs-primary);
    }

    .empresa-card.selected {
        border-color: var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }

    .local-card {
        transition: all 0.2s ease;
        cursor: pointer;
        border: 2px solid #dee2e6;
    }

    .local-card:hover {
        border-color: var(--bs-secondary);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    .local-card.selected {
        border-color: var(--bs-secondary);
        background-color: rgba(var(--bs-secondary-rgb), 0.05);
    }
</style>
@endpush

@push('scripts')
<script>
    let empresaSeleccionada = {{ $empresaActual ?? 'null' }};

    // Cargar locales si hay empresa pre-seleccionada
    @if($empresaActual)
        document.addEventListener('DOMContentLoaded', function() {
            cargarLocales({{ $empresaActual }});
        });
    @endif

    function seleccionarEmpresa(empresaId) {
        // Actualizar selección visual
        document.querySelectorAll('.empresa-card').forEach(card => {
            card.classList.remove('selected', 'border-primary');
        });
        
        event.currentTarget.classList.add('selected', 'border-primary');
        
        // Marcar radio button
        document.getElementById('empresa_' + empresaId).checked = true;
        
        // Guardar selección
        empresaSeleccionada = empresaId;
        
        // Cargar locales
        cargarLocales(empresaId);
    }

    function cargarLocales(empresaId) {
        const localesCard = document.getElementById('locales-card');
        const localesLoading = document.getElementById('locales-loading');
        const localesContainer = document.getElementById('locales-container');
        const sinLocales = document.getElementById('sin-locales');

        // Mostrar card y loading
        localesCard.style.display = 'block';
        localesLoading.style.display = 'block';
        localesContainer.innerHTML = '';
        sinLocales.style.display = 'none';

        // Fetch locales
        fetch(`{{ route('contexto.locales') }}?empresa_id=${empresaId}`)
            .then(response => response.json())
            .then(data => {
                localesLoading.style.display = 'none';

                if (data.success && data.locales.length > 0) {
                    // Renderizar locales
                    data.locales.forEach(local => {
                        const isActual = {{ $localActual ?? 'null' }} == local.id;
                        const isPrincipal = local.es_principal;

                        const localCard = `
                            <div class="col-md-6">
                                <div class="card h-100 local-card ${isActual ? 'selected border-secondary' : ''}" 
                                     onclick="seleccionarLocal(${local.id})">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-map-marker-alt text-secondary me-2"></i>
                                            ${local.nombre}
                                            ${isPrincipal ? '<span class="badge bg-success ms-2">Principal</span>' : ''}
                                            ${isActual ? '<span class="badge bg-secondary ms-2">Actual</span>' : ''}
                                        </h6>
                                        <p class="text-muted small mb-0">
                                            ${local.direccion || 'Sin dirección'}
                                        </p>
                                        <input type="radio" 
                                               name="local_id" 
                                               value="${local.id}" 
                                               id="local_${local.id}"
                                               class="d-none"
                                               ${isActual ? 'checked' : ''}>
                                    </div>
                                </div>
                            </div>
                        `;
                        localesContainer.innerHTML += localCard;
                    });

                    // Agregar opción "Sin local"
                    const sinLocalCard = `
                        <div class="col-md-6">
                            <div class="card h-100 local-card" onclick="seleccionarLocal(null)">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-times-circle text-muted me-2"></i>
                                        Sin local específico
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        Trabajar sin un local específico
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    localesContainer.innerHTML += sinLocalCard;

                } else {
                    sinLocales.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error al cargar locales:', error);
                localesLoading.style.display = 'none';
                sinLocales.style.display = 'block';
            });
    }

    function seleccionarLocal(localId) {
        // Actualizar selección visual
        document.querySelectorAll('.local-card').forEach(card => {
            card.classList.remove('selected', 'border-secondary');
        });
        
        event.currentTarget.classList.add('selected', 'border-secondary');
        
        // Marcar o desmarcar radio buttons
        document.querySelectorAll('input[name="local_id"]').forEach(radio => {
            radio.checked = false;
        });
        
        if (localId) {
            document.getElementById('local_' + localId).checked = true;
        }
    }

    // Validar formulario antes de enviar
    document.getElementById('contexto-form').addEventListener('submit', function(e) {
        if (!empresaSeleccionada) {
            e.preventDefault();
            alert('Por favor, selecciona una empresa');
            return false;
        }
    });
</script>
@endpush
@endsection
