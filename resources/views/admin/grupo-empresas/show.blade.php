@extends('layouts.adm')

@section('title', 'Detalle del Grupo Empresarial')

@php
    $breadcrumbs = [
        'title' => 'Detalle del Grupo Empresarial',
        'description' => 'Información completa del grupo empresarial',
        'icon' => 'ti ti-building-bank',
        'items' => [
            ['name' => 'Configuración del Sistema', 'url' => 'javascript:void(0)'],
            ['name' => 'Grupos Empresariales', 'url' => route('admin.grupo-empresarial.index')],
            ['name' => $grupoEmpresarial->nombre, 'url' => 'javascript:void(0)'],
        ],
        'actions' => [
            [
                'name' => 'Regresar',
                'url' => route('admin.grupo-empresarial.index'),
                'typeButton' => 'btn-label-dark',
                'icon' => 'ti ti-arrow-left',
            ],
            [
                'name' => 'Editar',
                'url' => route('admin.grupo-empresarial.edit', $grupoEmpresarial),
                'typeButton' => 'btn-primary',
                'icon' => 'ti ti-edit',
            ],
        ],
    ];
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <x-erp.breadcrumbs :items="$breadcrumbs">

        <x-slot:acciones>
            @can('grupo-empresarial.view')
            <a href="{{ route('admin.grupo-empresarial.index') }}" class="btn btn-label-dark waves-effect">
                <i class="ti ti-arrow-left me-2"></i>
                Regresar
            </a>
            @endcan
            @can('grupo-empresarial.edit')
                <a href="{{ route('admin.grupo-empresarial.edit', $grupoEmpresarial) }}" class="btn btn-warning waves-effect">
                    <i class="ti ti-edit me-2"></i>
                    Editar
                </a>
            @endcan
        </x-slot:acciones>

    </x-erp.breadcrumbs>

    <div class="row">
        <div class="col-lg-8">
            <!-- Información General -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-info-circle me-2"></i>
                        Información General
                    </h5>
                    <span class="badge bg-{{ $grupoEmpresarial->estado ? 'success' : 'danger' }}">
                        {{ $grupoEmpresarial->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Nombre:</td>
                                    <td>{{ $grupoEmpresarial->nombre }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Código:</td>
                                    <td><span class="badge bg-label-info">{{ $grupoEmpresarial->codigo }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">País de Origen:</td>
                                    <td>{{ $grupoEmpresarial->pais_origen ?? 'No especificado' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Teléfono:</td>
                                    <td>{{ $grupoEmpresarial->telefono ?? 'No especificado' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Email:</td>
                                    <td>
                                        @if($grupoEmpresarial->email)
                                            <a href="mailto:{{ $grupoEmpresarial->email }}">{{ $grupoEmpresarial->email }}</a>
                                        @else
                                            No especificado
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Sitio Web:</td>
                                    <td>
                                        @if($grupoEmpresarial->sitio_web)
                                            <a href="{{ $grupoEmpresarial->sitio_web }}" target="_blank" rel="noopener">
                                                {{ $grupoEmpresarial->sitio_web }}
                                                <i class="ti ti-external-link ms-1"></i>
                                            </a>
                                        @else
                                            No especificado
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($grupoEmpresarial->descripcion)
                        <div class="mt-3">
                            <h6 class="fw-bold text-muted">Descripción:</h6>
                            <p class="mb-0">{{ $grupoEmpresarial->descripcion }}</p>
                        </div>
                    @endif

                    @if($grupoEmpresarial->direccion_matriz)
                        <div class="mt-3">
                            <h6 class="fw-bold text-muted">Dirección Matriz:</h6>
                            <p class="mb-0">{{ $grupoEmpresarial->direccion_matriz }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Empresas Asociadas -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-building me-2"></i>
                        Empresas Asociadas
                    </h5>
                    <span class="badge bg-primary">{{ $grupoEmpresarial->empresas->count() }} empresa(s)</span>
                </div>
                <div class="card-body">
                    @if($grupoEmpresarial->empresas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>RUC/NIT</th>
                                        <th>País</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grupoEmpresarial->empresas as $empresa)
                                        <tr>
                                            <td>
                                                <strong>{{ $empresa->nombre }}</strong>
                                                @if($empresa->nombre_comercial)
                                                    <br><small class="text-muted">{{ $empresa->nombre_comercial }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $empresa->numerodocumento }}</td>
                                            <td>{{ $empresa->pais->nombre ?? 'No especificado' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $empresa->estado ? 'success' : 'danger' }}">
                                                    {{ $empresa->estado ? 'Activa' : 'Inactiva' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-building display-4 text-muted"></i>
                            <h6 class="mt-3">No hay empresas asociadas</h6>
                            <p class="text-muted">Este grupo empresarial aún no tiene empresas asociadas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Estadísticas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-chart-bar me-2"></i>
                        Estadísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-building"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $grupoEmpresarial->empresas->count() }}</h6>
                            <small class="text-muted">Empresas Totales</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-check"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $grupoEmpresarial->empresas->where('estado', true)->count() }}</h6>
                            <small class="text-muted">Empresas Activas</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ti ti-x"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $grupoEmpresarial->empresas->where('estado', false)->count() }}</h6>
                            <small class="text-muted">Empresas Inactivas</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Sistema -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-clock me-2"></i>
                        Información del Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold">ID:</td>
                            <td>{{ $grupoEmpresarial->id }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Creado:</td>
                            <td>{{ $grupoEmpresarial->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Actualizado:</td>
                            <td>{{ $grupoEmpresarial->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($grupoEmpresarial->updated_at != $grupoEmpresarial->created_at)
                            <tr>
                                <td class="fw-bold">Última modificación:</td>
                                <td>{{ $grupoEmpresarial->updated_at->diffForHumans() }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection