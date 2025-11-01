@extends('layouts.vuexy')

@section('title', 'Panel de Administración')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">
                                <i class="ti ti-shield-check me-2"></i>Panel de Superusuario
                            </h4>
                            <p class="mb-0 text-muted">Gestión global del sistema</p>
                        </div>
                        <div>
                            <span class="badge bg-label-primary">
                                <i class="ti ti-crown me-1"></i>Superadministrador
                            </span>
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
                            <p class="card-text">Grupos Empresariales</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="mb-0 me-2">{{ $stats['total_grupos'] }}</h4>
                                <small class="text-success">({{ $stats['grupos_activos'] }} activos)</small>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-building-community ti-sm"></i>
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
                            <p class="card-text">Usuarios Totales</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="mb-0 me-2">{{ $stats['total_usuarios'] }}</h4>
                                <small class="text-success">({{ $stats['usuarios_activos'] }} activos)</small>
                            </div>
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
                            <p class="card-text">Sistema</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="mb-0 me-2">
                                    <span class="badge bg-success">Operativo</span>
                                </h4>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-success rounded-pill p-2">
                                <i class="ti ti-heartbeat ti-sm"></i>
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
                            <p class="card-text">Accesos Rápidos</p>
                            <div class="d-flex gap-2 mt-2">
                                <a href="{{ route('admin.configuracion') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-settings"></i>
                                </a>
                                <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ti ti-file-text"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-warning rounded-pill p-2">
                                <i class="ti ti-bolt ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Grupos recientes --}}
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti ti-building-community me-2"></i>Grupos Empresariales Recientes
                    </h5>
                    <a href="{{ route('admin.grupos.index') }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-eye me-1"></i>Ver todos
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Grupo</th>
                                    <th>Plan</th>
                                    <th>Empresas</th>
                                    <th>Usuarios</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gruposRecientes as $grupo)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $grupo->nombre }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $grupo->slug }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $grupo->plan_actual }}</span>
                                    </td>
                                    <td>{{ $grupo->empresas()->count() }} / {{ $grupo->max_empresas }}</td>
                                    <td>{{ $grupo->usuarios()->count() }} / {{ $grupo->max_usuarios }}</td>
                                    <td>
                                        @if($grupo->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.grupos.show', $grupo) }}" class="btn btn-sm btn-icon btn-text-secondary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.grupos.edit', $grupo) }}" class="btn btn-sm btn-icon btn-text-secondary">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="ti ti-mood-sad mb-2" style="font-size: 2rem;"></i>
                                        <p class="mb-0">No hay grupos registrados</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actividad reciente --}}
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-activity me-2"></i>Actividad Reciente
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <ul class="timeline mb-0">
                        @forelse($actividadReciente as $actividad)
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-primary"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">{{ $actividad->description }}</h6>
                                    <small class="text-muted">{{ $actividad->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-2">
                                    <small>
                                        Por: {{ $actividad->causer->name ?? 'Sistema' }}
                                    </small>
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
    </div>

</div>
@endsection
