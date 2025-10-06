@extends('layouts.app-ws')

@section('title', 'Aplicaciones - ERP Multisoft')

@section('ui-vendor-styles')
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/css/pages/cards-advance.css') }}" />
    <style>
        .app-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(var(--bs-gray-300-rgb), 0.5);
        }
        .app-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .app-card.disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .empresa-item {
            transition: background-color 0.2s ease;
            border-radius: 8px;
        }
        .empresa-item:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }
        .badge-status {
            font-size: 0.75rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="text-white mb-2">¡Bienvenido {{ $user->name }}! 🎉</h4>
                                <p class="text-white mb-0">Selecciona una aplicación y empresa para comenzar a trabajar</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <i class="ti ti-apps ti-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aplicaciones Disponibles -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="ti ti-apps me-2"></i>
                        Aplicaciones Disponibles
                    </h5>
                    <span class="badge bg-label-primary">{{ count($aplicaciones) }} aplicaciones</span>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            @foreach($aplicaciones as $app)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
                <div class="card app-card {{ !$app['activa'] ? 'disabled' : '' }}" 
                     data-app="{{ $app['id'] }}"
                     style="cursor: {{ $app['activa'] ? 'pointer' : 'not-allowed' }};">
                    <div class="card-body text-center p-4">
                        <div class="avatar avatar-lg mx-auto mb-3">
                            <div class="avatar-initial bg-label-{{ $app['color'] }} rounded">
                                <i class="{{ $app['icono'] }} ti-md"></i>
                            </div>
                        </div>
                        <h5 class="mb-2">{{ $app['nombre'] }}</h5>
                        <p class="text-muted small mb-3">{{ $app['descripcion'] }}</p>
                        <div class="d-flex justify-content-center">
                            @if($app['activa'])
                                <span class="badge bg-label-success badge-status">
                                    <i class="ti ti-check me-1"></i>Disponible
                                </span>
                            @else
                                <span class="badge bg-label-secondary badge-status">
                                    <i class="ti ti-clock me-1"></i>Próximamente
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Empresas Asociadas -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="ti ti-building me-2"></i>
                        Mis Empresas
                    </h5>
                    <span class="badge bg-label-info">{{ $empresas->count() }} {{ $empresas->count() == 1 ? 'empresa' : 'empresas' }}</span>
                </div>
            </div>
        </div>
        
        @if($empresas->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">
                                    <i class="ti ti-building-bank me-2 text-primary"></i>
                                    {{ $grupoActual->nombre }}
                                </h6>
                                @if($grupoActual->ruc)
                                    <small class="text-muted">RUC: {{ $grupoActual->ruc }}</small>
                                @endif
                            </div>
                            <div>
                                <span class="badge bg-label-success">{{ $empresas->count() }} {{ $empresas->count() == 1 ? 'empresa' : 'empresas' }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($grupoActual->razon_social)
                                <p class="text-muted mb-3">{{ $grupoActual->razon_social }}</p>
                            @endif
                            
                            @if($empresas->count() > 0)
                                <div class="row">
                                    @foreach($empresas as $empresa)
                                    <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                                        <div class="empresa-item p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-1">
                                                    <i class="ti ti-building-store me-2 text-info"></i>
                                                    {{ $empresa->nombre_comercial ?? $empresa->razon_social }}
                                                </h6>
                                                <span class="badge bg-label-success badge-sm">Activa</span>
                                            </div>
                                            
                                            @if($empresa->ruc)
                                                <p class="text-muted small mb-2">
                                                    <i class="ti ti-id me-1"></i>
                                                    RUC: {{ $empresa->ruc }}
                                                </p>
                                            @endif
                                            
                                            @if($empresa->direccion)
                                                <p class="text-muted small mb-2">
                                                    <i class="ti ti-map-pin me-1"></i>
                                                    {{ Str::limit($empresa->direccion, 40) }}
                                                </p>
                                            @endif

                                            <!-- Aplicaciones disponibles para esta empresa -->
                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                @foreach(array_filter($aplicaciones, function($app) { return $app['activa']; }) as $app)
                                                <a href="{{ route('empresa.dashboard', ['grupo' => $grupoActual->slug, 'empresa' => $empresa->id]) }}" 
                                                   class="btn btn-outline-{{ $app['color'] }} btn-sm"
                                                   title="Acceder a {{ $app['nombre'] }}">
                                                    <i class="{{ $app['icono'] }} me-1"></i>
                                                    {{ $app['nombre'] }}
                                                </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="ti ti-building-off ti-lg text-muted mb-2"></i>
                                    <p class="text-muted">No hay empresas registradas en este grupo</p>
                                    <a href="{{ route('grupo.empresas.create', $grupoActual->slug) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-plus me-1"></i>
                                        Agregar Empresa
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Estado vacío -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="avatar avatar-xl mx-auto mb-3">
                                <div class="avatar-initial bg-label-warning rounded">
                                    <i class="ti ti-building-off ti-lg"></i>
                                </div>
                            </div>
                            <h5 class="mb-2">No tienes empresas asociadas</h5>
                            <p class="text-muted mb-4">
                                Para comenzar a usar las aplicaciones, necesitas tener al menos una empresa registrada en tu grupo empresarial.
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="javascript:void(0)" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i>
                                    Crear Grupo Empresarial
                                </a>
                                <a href="javascript:void(0)" class="btn btn-outline-secondary">
                                    <i class="ti ti-help me-1"></i>
                                    Contactar Soporte
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Información adicional -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-label-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-info-circle me-3 ti-lg text-info"></i>
                            <div>
                                <h6 class="mb-1">¿Necesitas ayuda?</h6>
                                <p class="mb-0 small">
                                    Si tienes problemas para acceder a alguna aplicación o necesitas registrar una nueva empresa, 
                                    contacta con nuestro equipo de soporte.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('ui-vendor-scripts')
    <script src="{{ asset('vuexy/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('ui-page-scripts')
    <script>
        $(document).ready(function() {
            // Efecto hover para las tarjetas de aplicaciones
            $('.app-card:not(.disabled)').on('click', function() {
                const appId = $(this).data('app');
                
                // Aquí puedes agregar lógica para mostrar más información
                // o redirigir a una página específica de la aplicación
                console.log('Aplicación seleccionada:', appId);
                
                // Ejemplo: mostrar modal con información adicional
                // $('#modalApp').modal('show');
            });

            // Tooltip para aplicaciones deshabilitadas
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Animación de entrada para las tarjetas
            $('.card').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
                $(this).addClass('animate__animated animate__fadeInUp');
            });
        });
    </script>
@endsection
