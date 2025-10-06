@extends('layouts.app-ws')

@section('title', 'Dashboard - ' . $grupoActual->nombre)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header con info del grupo --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="text-white mb-1">
                                <i class="ti ti-building-community me-2"></i>{{ $grupoActual->nombre }}
                            </h4>
                            <p class="mb-0">{{ $grupoActual->razon_social ?? 'Panel de Administración del Grupo' }}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-white text-primary mb-2">
                                <i class="ti ti-crown me-1"></i>Plan {{ ucfirst($grupoActual->plan_actual) }}
                            </span>
                            <p class="mb-0 small">
                                <i class="ti ti-clock me-1"></i>
                                {{-- {{ $grupoActual->diasRestantesPlan() ?? 0 }} días restantes --}}
                            </p>
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
                            <p class="card-text mb-1">Empresas</p>
                            <h4 class="mb-0">{{ $stats['empresas_activas'] }} / {{ $grupoActual->max_empresas }}</h4>
                            <small class="text-muted">{{ $stats['total_empresas'] }} totales</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-building ti-sm"></i>
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
                            <p class="card-text mb-1">Usuarios</p>
                            <h4 class="mb-0">{{ $stats['usuarios_activos'] }} / {{ $grupoActual->max_usuarios }}</h4>
                            <small class="text-muted">{{ $stats['total_usuarios'] }} totales</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-info rounded-pill p-2">
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
                            <p class="card-text mb-1">Sedes</p>
                            <h4 class="mb-0">{{ $stats['total_sedes'] }}</h4>
                            <small class="text-muted">Ubicaciones</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-success rounded-pill p-2">
                                <i class="ti ti-map-pin ti-sm"></i>
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
                            <h4 class="mb-0">{{ $stats['total_locales'] }}</h4>
                            <small class="text-muted">Puntos de venta</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-warning rounded-pill p-2">
                                <i class="ti ti-home ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Empresas del grupo --}}
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti ti-building me-2"></i>Empresas del Grupo
                    </h5>
                    <a href="{{ route('grupo.empresas.create', ['grupo' => $grupoActual->slug]) }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-plus me-1"></i>Nueva Empresa
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Empresa</th>
                                    <th>RUC</th>
                                    <th>Usuarios</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($empresas as $empresa)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $empresa->nombre }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $empresa->slug }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $empresa->ruc ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-label-info">{{ $empresa->usuarios_count }}</span>
                                    </td>
                                    <td>
                                        @if($empresa->activo)
                                            <span class="badge bg-success">Activa</span>
                                        @else
                                            <span class="badge bg-danger">Inactiva</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('grupo.dashboard', ['grupo' => $grupoActual->slug, 'empresa' => $empresa->id]) }}" 
                                           class="btn btn-icon btn-text-primary" title="Ver Dashboard">
                                            <i class="ti ti-dashboard"></i>
                                        </a>
                                        <a href="{{ route('grupo.empresas.show', ['grupo' => $grupoActual->slug, 'empresa' => $empresa->id]) }}" 
                                           class="btn btn-icon btn-text-secondary" title="Ver Detalles">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('grupo.empresas.edit', ['grupo' => $grupoActual->slug, 'empresa' => $empresa->id]) }}" 
                                           class="btn btn-icon btn-text-secondary" title="Editar">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="ti ti-building mb-2" style="font-size: 2rem;"></i>
                                        <p class="mb-2">No hay empresas registradas</p>
                                        <a href="{{ route('grupo.empresas.create', ['grupo' => $grupoActual->slug]) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus me-1"></i>Crear Primera Empresa
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actividad reciente y usuarios --}}
        <div class="col-lg-4 mb-4">
            {{-- Usuarios recientes --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-users me-2"></i>Usuarios Recientes
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($usuariosRecientes->take(5) as $usuario)
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ $usuario->avatar_url }}" alt="Avatar" 
                                     class="rounded-circle me-2" width="32" height="32">
                                <div class="flex-grow-1">
                                    <small class="d-block fw-semibold">{{ $usuario->name }}</small>
                                    <small class="text-muted">{{ $usuario->rol_principal ?? 'Sin rol' }}</small>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="text-center py-3">
                            <small class="text-muted">No hay usuarios registrados</small>
                        </li>
                        @endforelse
                    </ul>
                    <a href="" 
                    {{-- <a href="{{ route('grupo.usuarios.index', ['grupo' => $grupoActual->slug]) }}"  --}}
                       class="btn btn-sm btn-outline-primary w-100 mt-2">
                        Ver Todos los Usuarios
                    </a>
                </div>
            </div>

            {{-- Actividad reciente --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-activity me-2"></i>Actividad Reciente
                    </h5>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <ul class="timeline mb-0">
                        @forelse($actividadReciente->take(10) as $actividad)
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-primary"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <small class="fw-medium">{{ $actividad->description }}</small>
                                    <small class="text-muted d-block">{{ $actividad->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="text-center py-3">
                            <small class="text-muted">No hay actividad reciente</small>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
