@extends('layouts.app-ws')

@section('title', 'Detalle de Empresa')

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
            ['name' => 'Detalle empresa', 'url' => '', 'active' => true],
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
            <x-slot:acciones>
                @can('editar_empresas')
                    <a href="{{ route('grupo.empresas.edit', ['grupo' => $grupoActual->slug ?? request()->route('grupo'), 'empresa' => $empresa->id]) }}"
                        class="btn btn-label-info waves-effect">
                        <i class="ti ti-pencil me-2"></i>
                        Editar Empresa
                    </a>
                @endcan
            </x-slot:acciones>

        </x-workspace.breadcrumbs>

        <div class="row">
            <!--/ User Sidebar -->
            <div class="col-xl-8 col-lg-7 order-0 order-md-0">
                <div class="card">

                    <x-workspace.card-header title="Información de la empresa"
                        description="{{ $empresa->nombre_comercial ?? $empresa->razon_social }}" textColor="text-info"
                        icon="ti ti-list-search" iconColor="bg-label-info" estado="{{ $empresa->activo ? '1' : '0' }}">

                        {{-- Botón de editar empresa --}}

                        @can('empresas.edit')
                            <a href="{{ route('grupo.empresas.edit', ['grupo' => $grupoActual->slug ?? request()->route('grupo'), 'empresa' => $empresa->id]) }}"
                                class="btn btn-primary waves-effect">
                                <i class="ti ti-edit me-2"></i>
                                Editar Empresa
                            </a>
                        @endcan
                    </x-workspace.card-header>

                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th class="bg-light" style="width: 35%;">Nombre Comercial</th>
                                            <td>{{ $empresa->nombre_comercial }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">RUC</th>
                                            <td>{{ $empresa->ruc }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Razón Social</th>
                                            <td>{{ $empresa->razon_social }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Dirección</th>
                                            <td>{{ $empresa->direccion }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Teléfono</th>
                                            <td>{{ $empresa->telefono ?? 'No registrado' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th class="bg-light" style="width: 35%;">CORREO</th>
                                            <td>
                                                @if ($empresa->correo)
                                                    <a href="mailto:{{ $empresa->correo }}">{{ $empresa->correo }}</a>
                                                @else
                                                    No registrado
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th class="bg-light">Estado</th>
                                            <td>
                                                <span class="badge bg-{{ $empresa->estado == 1 ? 'success' : 'danger' }}">
                                                    {{ $empresa->estado == 1 ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Fecha de Registro</th>
                                            <td>{{ $empresa->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Última Actualización</th>
                                            <td>{{ $empresa->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Sección de actividades del log --}}
                        @if ($activities->isNotEmpty())
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">
                                                <i class="ti ti-history text-primary"></i>
                                                Historial de Actividades
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover table-bordered text-center">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th>FECHA REGISTRO</th>
                                                            <th>ACCIÓN</th>
                                                            <th>USUARIO</th>
                                                            <th>CAMBIOS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($activities as $activity)
                                                            <tr>
                                                                <td>
                                                                    <small class="text-muted">
                                                                        {{ $activity->created_at->format('d/m/Y') }}<br>
                                                                        {{ $activity->created_at->format('H:i:s') }}
                                                                    </small>
                                                                </td>
                                                                <td>
                                                                    @switch($activity->description)
                                                                        @case('created')
                                                                            <span class="badge bg-label-success bg-glow">
                                                                                <i class="ti ti-plus"></i> Creado
                                                                            </span>
                                                                        @break

                                                                        @case('updated')
                                                                            <span class="badge bg-label-warning bg-glow">
                                                                                <i class="ti ti-edit"></i> Actualizado
                                                                            </span>
                                                                        @break

                                                                        @case('deleted')
                                                                            <span class="badge bg-label-danger bg-glow">
                                                                                <i class="ti ti-trash"></i> Eliminado
                                                                            </span>
                                                                        @break

                                                                        @default
                                                                            <span class="badge bg-label-info bg-glow">
                                                                                <i class="ti ti-info-circle"></i>
                                                                                {{ ucfirst($activity->description) }}
                                                                            </span>
                                                                    @endswitch
                                                                </td>
                                                                <td>
                                                                    @if ($activity->causer)
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="avatar avatar-xs me-2">
                                                                                <span
                                                                                    class="avatar-initial rounded-circle bg-label-primary">
                                                                                    {{ substr($activity->causer->name, 0, 1) }}
                                                                                </span>
                                                                            </div>
                                                                            <div>
                                                                                <small
                                                                                    class="fw-medium">{{ $activity->causer->name }}</small><br>
                                                                                <small
                                                                                    class="text-muted">{{ $activity->causer->email }}</small>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted">Sistema</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($activity->properties && $activity->properties->has('attributes'))
                                                                        <button
                                                                            class="btn btn-outline-primary waves-effect"
                                                                            type="button" data-bs-toggle="collapse"
                                                                            data-bs-target="#changes-{{ $activity->id }}"
                                                                            aria-expanded="false">
                                                                            <i class="ti ti-eye me-2"></i> Ver cambios
                                                                        </button>
                                                                        <div class="collapse mt-2"
                                                                            id="changes-{{ $activity->id }}">
                                                                            <div
                                                                                class="card card-body bg-light text-start">
                                                                                @if ($activity->properties->has('old') && $activity->properties->has('attributes'))
                                                                                    @php
                                                                                        $old = $activity->properties->get(
                                                                                            'old',
                                                                                            [],
                                                                                        );
                                                                                        $new = $activity->properties->get(
                                                                                            'attributes',
                                                                                            [],
                                                                                        );
                                                                                    @endphp
                                                                                    @foreach ($new as $key => $value)
                                                                                        @if (isset($old[$key]) || $old[$key] != $value)
                                                                                            <div class="mb-1">
                                                                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong><br>
                                                                                                <small
                                                                                                    class="text-danger">Antes:
                                                                                                    {{ $old[$key] ?? 'N/A' }}</small><br>
                                                                                                <small
                                                                                                    class="text-success">Después:
                                                                                                    {{ $value }}</small>
                                                                                            </div>
                                                                                            <hr class="my-1">
                                                                                        @endif
                                                                                    @endforeach
                                                                                @else
                                                                                    @foreach ($activity->properties->get('attributes', []) as $key => $value)
                                                                                        <div class="mb-1">
                                                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                            {{ $value }}
                                                                                        </div>
                                                                                    @endforeach
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <small class="text-muted">Sin detalles</small>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            @if ($activities->count() >= 10)
                                                <div class="text-center mt-3">
                                                    <small class="text-muted">Mostrando las 10 actividades más
                                                        recientes</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="ti ti-circle-info"></i>
                                        No hay actividades registradas para esta empresa.
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                @can('empresas.delete')
                                    <form method="POST"
                                        action="{{ route('grupo.empresas.destroy', ['grupo' => $grupoActual->slug ?? request()->route('grupo'), 'empresa' => $empresa->id]) }}"
                                        style="display: inline;"
                                        onsubmit="return confirm('¿Estás seguro de eliminar esta empresa? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="ti ti-trash"></i> Eliminar Empresa
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- User Sidebar -->
            <div class="col-xl-4 col-lg-5 order-1 order-md-1">
                <!-- User Card -->
                <div class="card mb-6">
                    <div class="card-body pt-12">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                @if($empresa->logo)
                                <img class="img-fluid rounded mb-4" src="{{ Storage::url($empresa->logo) }}" height="120"
                                    width="120" alt="User avatar" />
                                    @else
                                    <div class="avatar avatar-xl">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($empresa->nombre_comercial, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="user-info text-center" title="Módulos Activos">
                                    <h5>{{ $empresa->nombre_comercial }}</h5>
                                    @if($empresa->modulos_activos)
                                        @foreach($empresa->modulos_activos as $modulo)
                                        <span class="badge bg-label-success me-1 text-uppercase">{{ $modulo }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-label-secondary">Sin módulos activos</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around flex-wrap my-6 gap-0 gap-md-3 gap-lg-4">
                            <div class="d-flex align-items-center me-5 gap-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="ti ti-users ti-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-0">1.23k</h5>
                                    <span>Usuarios</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="ti ti-building-store ti-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-0">568</h5>
                                    <span>Locales</span>
                                </div>
                            </div>
                        </div>
                        <div class="info-container">
                            <div class="col-lg-12 mb-6 mb-xl-0">
                                <div class="demo-inline-spacing my-4">
                                    <div class="list-group">
                                        <a href="javascript:void(0);" class="list-group-item list-group-item-action active waves-effect">
                                            Datos protegidos
                                        </a>
                                        <a href="javascript:void(0);" class="list-group-item list-group-item-action disabled waves-effect">
                                            Propietario: {{ $empresa->propietario }}
                                        </a>
                                        <a href="javascript:void(0);" class="list-group-item list-group-item-action disabled waves-effect">
                                            RUC: {{ $empresa->ruc }}
                                        </a>
                                        <a href="javascript:void(0);" class="list-group-item list-group-item-action disabled waves-effect">
                                            Idiomas: Español
                                        </a>
                                        <a href="javascript:void(0);" class="list-group-item list-group-item-action disabled waves-effect">
                                            País: {{ $empresa->pais }}
                                        </a>
                                        <a href="javascript:void(0);" class="list-group-item list-group-item-action disabled waves-effect">
                                            Estado: {{ $empresa->activo ? 'Activo' : 'Inactivo' }}
                                        </a>
                                        <a href="javascript:void(0);" class="list-group-item list-group-item-action disabled waves-effect">
                                            URL (ruta de acceso): {{ URL::to('/') }}/{{ $grupoActual->slug }}/ERP/{{ $empresa->slug }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                {{-- <a href="javascript:;" class="btn btn-label-primary me-4" data-bs-target="#editUser" data-bs-toggle="modal">Solicitar Edición</a> --}}
                                <a href="javascript:;" class="btn btn-label-danger suspend-user text-uppercase">Suspender Empresa</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /User Card -->
            </div>
            
        </div>
    </div>

@endsection
