@extends('layouts.app-erp')

@section('title', $sede->nombre . ' - Detalles de Sede')

@php
    $breadcrumbs = [
        'title' => 'Gestión de Sedes',
        'description' => 'Información completa de la sede',
        'icon' => 'ti ti-building-bank',
        'items' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Sedes', 'url' => route('sedes.index')],
            ['name' => 'Detalle sede', 'url' => route('sedes.show', $sede), 'active' => true],
        ],
        'actions' => [
            [
                'name' => 'Regresar',
                'url' => route('sedes.index'),
                'typeButton' => 'btn-label-dark',
                'icon' => 'ti ti-arrow-left',
                'permission' => 'sedes.view'
            ],

        ],
        // 'stats' => [
            //     [
            //         'name' => 'Estado',
            //         'value' => $sede->estado ? 'Activo' : 'Inactivo',
            //         'icon' => 'ti ti-' . ($sede->estado ? 'check' : 'x'),
            //         'color' => $sede->estado ? 'success' : 'danger'
            //     ],
            //     [
            //         'name' => 'Empresa',
            //         'value' => $sede->empresa->nombre_comercial ?? $sede->empresa->razon_social,
            //         'icon' => 'ti ti-building',
            //         'color' => 'primary'
            //     ],
        // ]
    ];

    $dataHeaderCard = [
        'title' => 'Información de la sede ',
        'description' => ($sede->nombre ?? ''),
        'textColor' => 'text-info',
        'icon' => 'ti ti-list-search',
        'iconColor' => 'bg-label-info',
        'actions' => [
            [
                'typeAction' => 'btnInfo',
                'name' => $sede->estado == 1 ? 'ACTIVO' : 'SUSPENDIDO',
                'url' => '#',
                'icon' => $sede->estado == 1 ? 'ti ti-check' : 'ti ti-x',
                'permission' => null,
                'typeButton' => $sede->estado == 1 ? 'btn-label-success' : 'btn-label-danger',
            ],
            [
                'typeAction' => 'btnLink',
                'name' => 'Editar',
                'url' => route('sedes.edit', $sede),
                'icon' => 'ti ti-edit',
                'permission' => 'sedes.edit'
            ],
        ],
    ];

@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb Component -->
        <x-erp.breadcrumbs :items="$breadcrumbs">
            <x-slot:extra>
                @can('sedes.view')
                <a href="{{ route('sedes.index') }}" class="btn btn-label-dark waves-effect">
                    <i class="ti ti-arrow-left me-2"></i>
                    Regresar
                </a>
                @endcan
            </x-slot:extra>
        </x-erp.breadcrumbs>

        <div class="row">
            {{-- Información Principal --}}
            <div class="col-lg-8">
                <div class="card mb-4">

                    <x-erp.card-header 
                        title="Información de la sedes" 
                        description="{{ $sede->nombre_comercial ?? $sede->razon_social }}"
                        textColor="text-info"
                        icon="ti ti-list-search"
                        iconColor="bg-label-info"
                        estado="{{ $sede->estado }}"
                    >                        
                        @can('sedes.edit')
                            <a href="{{ route('sedes.edit', $sede) }}" class="btn btn-primary waves-effect">
                                <i class="ti ti-edit me-2"></i>
                                Editar Sede
                            </a>                        
                        @endcan
                    </x-erp.card-header>

                    <div class="card-body">
                        {{-- Información Básica --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium text-muted small">NOMBRE DE LA SEDE</label>
                                    <p class="mb-0 fs-5">{{ $sede->nombre }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium text-muted small">CÓDIGO DE LA SEDE</label>
                                    <p class="mb-0 fs-5">{{ $sede->codigo }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Descripción --}}
                        @if($sede->descripcion)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium text-muted small">DESCRIPCIÓN</label>
                                        <div class="p-3 bg-light rounded">
                                            <p class="mb-0">{{ $sede->descripcion }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Información de la Empresa --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-medium text-muted small">EMPRESA ASOCIADA</label>
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle bg-label-info">
                                                    {{ substr($sede->empresa->razon_social, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $sede->empresa->nombre_comercial ?? $sede->empresa->razon_social }}</h6>
                                                <div class="row text-sm">
                                                    <div class="col-md-6">
                                                        <small class="text-muted">
                                                            <i class="ti ti-file-text me-1"></i>
                                                            <strong>RUC:</strong> {{ $sede->empresa->numerodocumento }}
                                                        </small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted">
                                                            <i class="ti ti-{{ $sede->empresa->estado ? 'check' : 'x' }} me-1"></i>
                                                            <strong>Estado:</strong> 
                                                            <span class="badge bg-{{ $sede->empresa->estado ? 'success' : 'danger' }} badge-sm">
                                                                {{ $sede->empresa->estado ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                @can('empresas.view')
                                                    <a href="{{ route('empresas.show', $sede->empresa) }}" 
                                                       class="btn btn-sm btn-outline-info"
                                                       title="Ver empresa">
                                                        <i class="ti ti-external-link"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Fechas --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium text-muted small">FECHA DE CREACIÓN</label>
                                    <p class="mb-0">
                                        <i class="ti ti-calendar me-1"></i>
                                        {{ $sede->created_at->format('d/m/Y \a \l\a\s H:i') }}
                                        <br>
                                        <small class="text-muted">
                                            ({{ $sede->created_at->diffForHumans() }})
                                        </small>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium text-muted small">ÚLTIMA MODIFICACIÓN</label>
                                    <p class="mb-0">
                                        <i class="ti ti-clock me-1"></i>
                                        {{ $sede->updated_at->format('d/m/Y \a \l\a\s H:i') }}
                                        <br>
                                        <small class="text-muted">
                                            ({{ $sede->updated_at->diffForHumans() }})
                                        </small>
                                    </p>
                                </div>
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
                                @can('sedes.delete')
                                    <form method="POST" 
                                            action="{{ route('sedes.destroy', $sede) }}" 
                                            class="d-inline"
                                            onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="ti ti-trash me-1"></i>
                                            Eliminar
                                        </button>
                                    </form>
                                @endcan
                            </div>
                            <div class="d-flex gap-2">
                                {{-- @can('sedes.edit')
                                    <a href="{{ route('sedes.edit', $sede) }}" class="btn btn-primary">
                                        <i class="ti ti-edit me-1"></i>
                                        Editar
                                    </a>
                                @endcan --}}
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Lateral --}}
            <div class="col-lg-4">
                {{-- Estadísticas rápidas --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="ti ti-chart-bar me-1"></i>
                            Información rápida
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="ti ti-building"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Sede de</small>
                                <h6 class="mb-0">{{ $sede->empresa->nombre_comercial ?? $sede->empresa->razon_social }}</h6>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-{{ $sede->estado ? 'success' : 'danger' }}">
                                    <i class="ti ti-{{ $sede->estado ? 'check' : 'x' }}"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Estado actual</small>
                                <h6 class="mb-0">{{ $sede->estado ? 'Activo' : 'Inactivo' }}</h6>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-info">
                                    <i class="ti ti-calendar"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Días activa</small>
                                <h6 class="mb-0">{{ $sede->created_at->diffInDays() }} días</h6>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Otras sedes de la misma empresa --}}
                @if($otrasSedesEmpresa->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="ti ti-list-details me-1"></i>
                                Otras sedes de la empresa
                                <span class="badge bg-label-primary ms-1">{{ $otrasSedesEmpresa->count() }}</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach($otrasSedesEmpresa as $otraSede)
                                <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded-circle bg-label-{{ $otraSede->estado ? 'success' : 'danger' }}">
                                            {{ substr($otraSede->nombre, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">{{ $otraSede->nombre }}</h6>
                                        <small class="text-muted">
                                            <span class="badge bg-{{ $otraSede->estado ? 'success' : 'danger' }} badge-sm">
                                                {{ $otraSede->estado ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </small>
                                    </div>
                                    <div>
                                        @can('sedes.view')
                                            <a href="{{ route('sedes.show', $otraSede) }}" 
                                               class="btn btn-xs btn-outline-primary"
                                               title="Ver detalles">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    return Swal.fire({
        title: '¿Eliminar sede?',
        html: `
            <div class="text-center">
                <p class="mb-2">¿Estás seguro de eliminar la sede <strong>"{{ $sede->nombre }}"</strong>?</p>
                <p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#8592a3'
    }).then((result) => {
        return result.isConfirmed;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush

@push('styles')
<style>
    .avatar-lg {
        width: 60px;
        height: 60px;
    }

    .avatar-lg .avatar-initial {
        font-size: 1.5rem;
        line-height: 60px;
    }

    .avatar-xs {
        width: 24px;
        height: 24px;
    }

    .avatar-xs .avatar-initial {
        font-size: 10px;
        line-height: 24px;
    }

    .btn-xs {
        padding: 0.125rem 0.375rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.25rem;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .badge-sm {
        font-size: 0.65em;
        padding: 0.25em 0.5em;
    }

    .card-body .border-bottom:last-child {
        border-bottom: none !important;
        padding-bottom: 0 !important;
    }
</style>
@endpush