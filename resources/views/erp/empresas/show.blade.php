@extends('layouts.vuexy')

@section('title', 'Detalle de Empresa')

@php
    $dataBreadcrumb = [
        'title' => 'Gestión de Empresas',
        'description' => 'Administra las empresas del sistema',
        'icon' => 'ti ti-building',
        'breadcrumbs' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Empresas', 'url' => route('empresas.index')],
            ['name' => 'Detalle empresa', 'url' => '', 'active' => true]
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
        'title' => 'Información de la empresa ',
        'description' => ($empresa->nombre_comercial ?? $empresa->razon_social),
        'textColor' => 'text-info',
        'icon' => 'ti ti-list-search',
        'iconColor' => 'bg-label-info',
        'actions' => [
            [
                'typeAction' => 'btnInfo',
                'name' => $empresa->estado == 1 ? 'ACTIVO' : 'SUSPENDIDO',
                'url' => '#',
                'icon' => $empresa->estado == 1 ? 'ti ti-check' : 'ti ti-x',
                'permission' => null,
                'typeButton' => $empresa->estado == 1 ? 'btn-label-success' : 'btn-label-danger',
            ],
            [
                'typeAction' => 'btnLink',
                'name' => 'Editar',
                'url' => route('empresas.edit', $empresa),
                'icon' => 'ti ti-edit',
                'permission' => 'empresas.edit'
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

                @include('layouts.vuexy.header-card', $dataHeaderCard)

                {{-- <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="mt-1">
                        <h4 class="card-title mb-0">{{ $empresa->nombre_comercial ?? $empresa->razon_social }}</h4>

                    </div>
                    <div>
                        <span class="btn btn-{{ $empresa->estado == 1 ? 'success' : 'danger' }}">
                            {{ $empresa->estado == 1 ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div> --}}

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
                                        <td>{{ $empresa->numerodocumento }}</td>
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
                                            @if($empresa->correo)
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
                    @if($activities->isNotEmpty())
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
                                                    @foreach($activities as $activity)
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
                                                                            <i class="ti ti-info-circle"></i> {{ ucfirst($activity->description) }}
                                                                        </span>
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                @if($activity->causer)
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar avatar-xs me-2">
                                                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                                                {{ substr($activity->causer->name, 0, 1) }}
                                                                            </span>
                                                                        </div>
                                                                        <div>
                                                                            <small class="fw-medium">{{ $activity->causer->name }}</small><br>
                                                                            <small class="text-muted">{{ $activity->causer->email }}</small>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">Sistema</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($activity->properties && $activity->properties->has('attributes'))
                                                                    <button class="btn btn-outline-primary waves-effect"
                                                                            type="button"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#changes-{{ $activity->id }}"
                                                                            aria-expanded="false">
                                                                        <i class="ti ti-eye me-2"></i> Ver cambios
                                                                    </button>
                                                                    <div class="collapse mt-2" id="changes-{{ $activity->id }}">
                                                                        <div class="card card-body bg-light text-start">
                                                                            @if($activity->properties->has('old') && $activity->properties->has('attributes'))
                                                                                @php
                                                                                    $old = $activity->properties->get('old', []);
                                                                                    $new = $activity->properties->get('attributes', []);
                                                                                @endphp
                                                                                @foreach($new as $key => $value)
                                                                                    @if(isset($old[$key]) || $old[$key] != $value)
                                                                                        <div class="mb-1">
                                                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong><br>
                                                                                            <small class="text-danger">Antes: {{ $old[$key] ?? 'N/A' }}</small><br>
                                                                                            <small class="text-success">Después: {{ $value }}</small>
                                                                                        </div>
                                                                                        <hr class="my-1">
                                                                                    @endif
                                                                                @endforeach
                                                                            @else
                                                                                @foreach($activity->properties->get('attributes', []) as $key => $value)
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

                                        @if($activities->count() >= 10)
                                            <div class="text-center mt-3">
                                                <small class="text-muted">Mostrando las 10 actividades más recientes</small>
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
                                        action="{{ route('empresas.destroy', $empresa) }}"
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
    </div>
</div>

@endsection
