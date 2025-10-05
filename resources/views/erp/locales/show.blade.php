@extends('layouts.vuexy')

@section('title', 'Ver Local')

@php
    $dataBreadcrumb = [
        'title' => 'Gestión de Locales',
        'description' => 'Información completa del local',
        'icon' => 'ti ti-building-bank',
        'breadcrumbs' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Locales', 'url' => route('locales.index')],
            ['name' => 'Detalle local', 'url' => 'javascript:void(0)'],
        ],
        'actions' => [
            [
                'name' => 'Regresar',
                'url' => route('locales.index'),
                'typeButton' => 'btn-label-dark',
                'icon' => 'ti ti-arrow-left',
                'permission' => 'locales.view'
            ],

        ],
    ];

    $dataHeaderCard = [
        'title' => 'Información del local ',
        'description' => ($local->descripcion ?? ''),
        'textColor' => 'text-info',
        'icon' => 'ti ti-list-search',
        'iconColor' => 'bg-label-info',
        'actions' => [
            [
                'typeAction' => 'btnInfo',
                'name' => $local->estado == 1 ? 'ACTIVO' : 'SUSPENDIDO',
                'url' => '#',
                'icon' => $local->estado == 1 ? 'ti ti-check' : 'ti ti-x',
                'permission' => null,
                'typeButton' => $local->estado == 1 ? 'btn-label-success' : 'btn-label-danger',
            ],
            [
                'typeAction' => 'btnLink',
                'name' => 'Editar',
                'url' => route('locales.edit', $local),
                'icon' => 'ti ti-edit',
                'permission' => 'locales.edit'
            ],
        ],
    ];

@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb Component -->
        @include('layouts.vuexy.breadcrumb', $dataBreadcrumb)

        {{-- <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Local: {{ $local->descripcion }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('locales.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('locales.index') }}">Locales</a></li>
                            <li class="breadcrumb-item active">Ver</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    {{-- header card --}}
                    @include('layouts.vuexy.header-card', $dataHeaderCard)

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-hover table-bordered">
                                    <tr>
                                        <td class="fw-bold bg-light" width="30%">Local:</td>
                                        <td>{{ $local->descripcion }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light">Código:</td>
                                        <td>
                                            <span class="badge bg-light text-dark fs-6">{{ $local->codigo }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light">Sede:</td>
                                        <td>
                                            {{ $local->sede->nombre ?? 'Sin sede asignada' }}
                                            @if ($local->sede)
                                                <br><small class="text-muted">{{ $local->sede->descripcion }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light">Dirección:</td>
                                        <td>
                                            @if ($local->direccion)
                                                {{ $local->direccion }}
                                            @else
                                                <span class="text-muted">No especificada</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-hover table-bordered">
                                    <tr>
                                        <td class="fw-bold bg-light" width="30%">Correo:</td>
                                        <td>
                                            @if ($local->correo)
                                                <a href="mailto:{{ $local->correo }}">{{ $local->correo }}</a>
                                            @else
                                                <span class="text-muted">No especificado</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light">Teléfono:</td>
                                        <td>
                                            @if ($local->telefono)
                                                <a href="tel:{{ $local->telefono }}">{{ $local->telefono }}</a>
                                            @else
                                                <span class="text-muted">No especificado</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light">WhatsApp:</td>
                                        <td>
                                            @if ($local->whatsapp)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $local->whatsapp) }}"
                                                    target="_blank">
                                                    {{ $local->whatsapp }}
                                                    <i class="bx bx-link-external ms-1"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">No especificado</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light">Estado:</td>
                                        <td>
                                            <span class="badge bg-{{ $local->estado ? 'success' : 'secondary' }}">
                                                <i class="bx bx-{{ $local->estado ? 'check' : 'x' }} me-1"></i>
                                                {{ $local->estado ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            {{-- <a href="{{ route('sedes.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-left me-1"></i>
                                Volver a la lista
                            </a> --}}
                            <div>
                                @can('locales.delete')
                                <button type="button" class="btn btn-danger"
                                    onclick="confirmDelete({{ $local->id }}, '{{ $local->descripcion }}')">
                                    <i class="ti ti-trash me-1"></i>
                                    Eliminar Local
                                </button>
                                @endcan
                            </div>
                            <div class="d-flex gap-2">
                                @can('locales.edit')
                                    <button type="button" class="btn btn-{{ $local->estado ? 'warning' : 'success' }}"
                                    onclick="toggleStatus({{ $local->id }}, {{ $local->estado ? 'false' : 'true' }})">
                                    <i class="ti ti-{{ $local->estado ? 'x' : 'check' }} me-1"></i>
                                    {{ $local->estado ? 'Desactivar' : 'Activar' }}
                                </button>
                                @endcan
                                
                            </div>
                        </div>
                    </div>

                </div>

                @if ($local->sede && $local->sede->empresa)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-buildings me-2"></i>
                                Información de la Empresa
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered table-hover table-sm">
                                        <tr class="">
                                            <td class="fw-bold bg-light" width="30%">Empresa:</td>
                                            <td>{{ $local->sede->empresa->razon_social }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-light">RUC:</td>
                                            <td>{{ $local->sede->empresa->numerodocumento }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered table-hover table-sm">
                                        <tr class="">
                                            <td class="fw-bold bg-light" width="30%">Sede:</td>
                                            <td>{{ $local->sede->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-light">Código:</td>
                                            <td>{{ $local->sede->codigo }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-time me-2"></i>
                            Información del Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td class="fw-bold">ID:</td>
                                <td>#{{ $local->id }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Creado:</td>
                                <td>
                                    {{ $local->created_at->format('d/m/Y H:i') }}
                                    <br><small class="text-muted">{{ $local->created_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Actualizado:</td>
                                <td>
                                    {{ $local->updated_at->format('d/m/Y H:i') }}
                                    <br><small class="text-muted">{{ $local->updated_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if ($local->direccion && config('services.google_maps.api_key'))
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-map me-2"></i>
                                Ubicación
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="ratio ratio-16x9">
                                <iframe
                                    src="https://maps.google.com/maps?q={{ urlencode($local->direccion) }}&output=embed"
                                    style="border:0;" allowfullscreen="" loading="lazy">
                                </iframe>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">{{ $local->direccion }}</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de confirmación de eliminación --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="bx bx-error-circle display-1 text-warning"></i>
                        <h5 class="mt-3">¿Estás seguro?</h5>
                        <p class="text-muted">
                            Vas a eliminar el local: <br>
                            <strong id="localToDelete"></strong>
                        </p>
                        <p class="text-danger">
                            <small>Esta acción no se puede deshacer.</small>
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-trash me-1"></i>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

{{-- @endsection

@section('page-script') --}}

    <script>
        function confirmDelete(localId, localName) {
            document.getElementById('localToDelete').textContent = localName;
            document.getElementById('deleteForm').action = `{{ route('locales.index') }}/${localId}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function toggleStatus(localId, newStatus) {
            if (confirm('¿Estás seguro de cambiar el estado del local?')) {
                // Implementar llamada AJAX aquí si es necesario
                window.location.href = `{{ route('locales.index') }}/${localId}/toggle-status`;
            }
        }
    </script>
@endsection
