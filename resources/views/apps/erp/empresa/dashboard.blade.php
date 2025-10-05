@extends('layouts.vuexy')

@section('title', 'Dashboard - ' . $empresa->nombre)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header con breadcrumb y selector de empresa --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            {{-- Breadcrumb --}}
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('grupo.dashboard', ['grupo' => $grupo->slug]) }}">
                                            {{ $grupo->nombre }}
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        {{ $empresa->nombre }}
                                    </li>
                                </ol>
                            </nav>
                            <h4 class="mb-0">
                                <i class="ti ti-building me-2"></i>{{ $empresa->nombre }}
                            </h4>
                        </div>
                        <div>
                            @if(isset($empresasConAcceso) && count($empresasConAcceso) > 1)
                            {{-- Selector de empresas --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-switch-horizontal me-1"></i>Cambiar Empresa
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach($empresasConAcceso as $emp)
                                    <li>
                                        <a class="dropdown-item {{ $emp->id == $empresa->id ? 'active' : '' }}" 
                                           href="{{ route('empresa.dashboard', ['grupo' => $grupo->slug, 'empresa' => $emp->slug]) }}">
                                            <i class="ti ti-building me-2"></i>{{ $emp->nombre }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="card-info">
                            <p class="card-text mb-1">Usuarios</p>
                            <h4 class="mb-0">{{ $stats['usuarios_activos'] }}</h4>
                            <small class="text-muted">{{ $stats['total_usuarios'] }} totales</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-users ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="card-info">
                            <p class="card-text mb-1">Locales</p>
                            <h4 class="mb-0">{{ $stats['locales_activos'] }}</h4>
                            <small class="text-muted">{{ $stats['total_locales'] }} totales</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-info rounded-pill p-2">
                                <i class="ti ti-home ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(in_array('ERP', $modulosActivos))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="card-info">
                            <p class="card-text mb-1">Ventas del Mes</p>
                            <h4 class="mb-0">S/ {{ number_format($stats['ventas_mes'] ?? 0, 2) }}</h4>
                            <small class="text-success">
                                <i class="ti ti-trending-up me-1"></i>+0%
                            </small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-success rounded-pill p-2">
                                <i class="ti ti-shopping-cart ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="card-info">
                            <p class="card-text mb-1">Stock Bajo</p>
                            <h4 class="mb-0">{{ $stats['productos_stock_bajo'] ?? 0 }}</h4>
                            <small class="text-warning">Productos</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-warning rounded-pill p-2">
                                <i class="ti ti-alert-triangle ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Módulos disponibles --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-apps me-2"></i>Módulos Disponibles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(in_array('ERP', $modulosActivos))
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6 mb-3">
                            <a href="{{ route('empresa.ventas.index', ['grupo' => $grupo->slug, 'empresa' => $empresa->slug]) }}" 
                               class="card card-hover text-center h-100">
                                <div class="card-body">
                                    <i class="ti ti-shopping-cart mb-2" style="font-size: 2rem; color: #696cff;"></i>
                                    <p class="mb-0 fw-semibold">Ventas</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-2 col-md-3 col-sm-4 col-6 mb-3">
                            <a href="{{ route('empresa.inventario.index', ['grupo' => $grupo->slug, 'empresa' => $empresa->slug]) }}" 
                               class="card card-hover text-center h-100">
                                <div class="card-body">
                                    <i class="ti ti-box mb-2" style="font-size: 2rem; color: #8592a3;"></i>
                                    <p class="mb-0 fw-semibold">Inventario</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-2 col-md-3 col-sm-4 col-6 mb-3">
                            <a href="{{ route('empresa.compras.index', ['grupo' => $grupo->slug, 'empresa' => $empresa->slug]) }}" 
                               class="card card-hover text-center h-100">
                                <div class="card-body">
                                    <i class="ti ti-truck mb-2" style="font-size: 2rem; color: #ff3e1d;"></i>
                                    <p class="mb-0 fw-semibold">Compras</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-xl-2 col-md-3 col-sm-4 col-6 mb-3">
                            <a href="{{ route('empresa.contabilidad.index', ['grupo' => $grupo->slug, 'empresa' => $empresa->slug]) }}" 
                               class="card card-hover text-center h-100">
                                <div class="card-body">
                                    <i class="ti ti-calculator mb-2" style="font-size: 2rem; color: #71dd37;"></i>
                                    <p class="mb-0 fw-semibold">Contabilidad</p>
                                </div>
                            </a>
                        </div>
                        @endif

                        @if(in_array('CRM', $modulosActivos))
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6 mb-3">
                            <a href="{{ route('empresa.clientes.index', ['grupo' => $grupo->slug, 'empresa' => $empresa->slug]) }}" 
                               class="card card-hover text-center h-100">
                                <div class="card-body">
                                    <i class="ti ti-users mb-2" style="font-size: 2rem; color: #03c3ec;"></i>
                                    <p class="mb-0 fw-semibold">CRM</p>
                                </div>
                            </a>
                        </div>
                        @endif

                        <div class="col-xl-2 col-md-3 col-sm-4 col-6 mb-3">
                            <a href="{{ route('empresa.reportes.index', ['grupo' => $grupo->slug, 'empresa' => $empresa->slug]) }}" 
                               class="card card-hover text-center h-100">
                                <div class="card-body">
                                    <i class="ti ti-chart-bar mb-2" style="font-size: 2rem; color: #ffab00;"></i>
                                    <p class="mb-0 fw-semibold">Reportes</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Actividad reciente --}}
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-activity me-2"></i>Actividad Reciente
                    </h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <ul class="timeline mb-0">
                        @forelse($actividadReciente as $actividad)
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-primary"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">{{ $actividad->description }}</h6>
                                    <small class="text-muted">{{ $actividad->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0">
                                    <small>Por: {{ $actividad->causer->name ?? 'Sistema' }}</small>
                                </p>
                            </div>
                        </li>
                        @empty
                        <li class="text-center py-4">
                            <small class="text-muted">No hay actividad reciente</small>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- Usuarios de la empresa --}}
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-users me-2"></i>Equipo de Trabajo
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($usuariosEmpresa as $usuario)
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ $usuario->avatar_url }}" alt="Avatar" 
                                     class="rounded-circle me-3" width="40" height="40">
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-semibold">{{ $usuario->name }}</p>
                                    <small class="text-muted">
                                        {{ $usuario->rol_principal ?? 'Sin rol' }}
                                        @if($usuario->ultimo_acceso)
                                            • Último acceso: {{ $usuario->ultimo_acceso->diffForHumans() }}
                                        @endif
                                    </small>
                                </div>
                                @if($usuario->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </div>
                        </li>
                        @empty
                        <li class="text-center py-4">
                            <i class="ti ti-users mb-2" style="font-size: 2rem;"></i>
                            <p class="mb-0 text-muted">No hay usuarios asignados a esta empresa</p>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.card-hover {
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
@endsection
