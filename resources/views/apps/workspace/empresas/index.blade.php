@extends('layouts.app-ws')

@section('title', 'Gestión de Empresas - ERP Multisoft')

@php
    $breadcrumbs = [
        'title' => 'Gestión de Empresas',
        'description' => '',
        'icon' => 'ti ti-building',
        'items' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Empresas', 'url' => route('grupo.empresas.index', ['grupo' => $grupoActual->slug ?? request()->route('grupo')])],
        ],
    ];
@endphp


@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <x-workspace.breadcrumbs :items="$breadcrumbs">

            <x-slot:extra>
                <div class="d-flex align-items-center">
                    <span class="badge bg-label-primary me-2">
                        <i class="ti ti-building"></i>
                    </span>
                    <span class="text-muted">Total Roles: {{ $totalEmpresas }}</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-label-success me-2">
                        <i class="ti ti-circle-check"></i>
                    </span>
                    <span class="text-muted">Roles Activas: {{ $empresasActivas }}</span>
                </div>
            </x-slot:extra>

        </x-workspace.breadcrumbs>

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header align-items-center justify-content-between border-bottom">
                        <div class="nav-align-top">
                            <ul class="nav nav-pills flex-column flex-md-row">
                                <li class="nav-item mb-2 mb-md-0 me-0 me-md-3">
                                    <a class="nav-link active" href="javascript:void(0);">
                                        <i class="ti-sm ti ti-building me-1"></i>
                                        Empresas
                                    </a>
                                </li>
                                <li class="nav-item mb-2 mb-md-0 me-0 me-md-3">
                                    <a class="nav-link border" href="{{ route('grupo.sedes.index', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]) }}">
                                        <i class="ti-xs ti ti-building-bank me-1"></i>
                                        Sedes
                                    </a>
                                </li>
                                <li class="nav-item mb-2 mb-md-0 me-0 me-md-3">
                                    <a class="nav-link border" href="pages-account-settings-billing.html">
                                        <i class="ti-xs ti ti-building-store me-1"></i>
                                        Locales
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <x-workspace.card-header 
                        title="Lista de Empresas" 
                        description=""
                        textColor="text-primary"
                        icon="ti ti-building"
                        iconColor="bg-label-primary"
                    >
                        @can('crear_empresas')
                            <a href="{{ route('grupo.empresas.create', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]) }}" class="btn btn-primary waves-effect">
                                <i class="ti ti-plus me-2"></i>
                                Crear Empresa
                            </a>
                        @endcan
                    </x-workspace.card-header>

                    <div class="card-body">
                        <!-- Filtros de búsqueda -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <form method="GET" action="{{ route('grupo.empresas.index', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]) }}" class="d-flex gap-3 align-items-end">
                                    <div class="flex-grow-1">
                                        <label for="buscar" class="form-label">Buscar empresa</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="buscar" 
                                               name="buscar" 
                                               value="{{ request('buscar') }}"
                                               placeholder="Buscar por razón social o RUC...">
                                    </div>
                                    <div class="flex-shrink-0" style="min-width: 150px;">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select class="form-select" id="estado" name="estado">
                                            <option value="">Todos</option>
                                            <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activas</option>
                                            <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivas</option>
                                        </select>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button type="submit" class="btn btn-label-primary">
                                            <i class="ti ti-search me-1"></i>
                                            Buscar
                                        </button>
                                    </div>
                                    @if(request('buscar') || request('estado') !== null)
                                        <div class="flex-shrink-0">
                                            <a href="{{ route('grupo.empresas.index', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]) }}" class="btn btn-label-secondary">
                                                <i class="ti ti-x me-1"></i>
                                                Limpiar
                                            </a>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- Mensajes de estado -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible d-flex" role="alert">
                                <span class="alert-icon rounded"><i class="ti ti-check"></i></span>
                                <div>
                                    <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">¡Éxito!</h6>
                                    <p class="mb-0">{{ session('success') }}</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible d-flex" role="alert">
                                <span class="alert-icon rounded"><i class="ti ti-x"></i></span>
                                <div>
                                    <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Error</h6>
                                    <p class="mb-0">{{ session('error') }}</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Tabla de empresas -->
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>ID</th>
                                        <th>Empresa</th>
                                        <th>RUC</th>
                                        <th>Correo</th>
                                        <th>Teléfono</th>
                                        <th class="text-center"></th>
                                        @canany(['editar_empresas', 'eliminar_empresas'])
                                            <th class="text-center">Acciones</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse($empresas as $empresa)
                                        <tr>
                                            <td>
                                                <span class="badge bg-label-secondary">#{{ $empresa->id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar me-3">
                                                            <span class="avatar-initial rounded-2 bg-label-primary">
                                                                @if ($empresa->logo)
                                                                    <img src="{{ Storage::url($empresa->logo) }}" alt="Logo" class="rounded-circle">
                                                                @else
                                                                    {{ strtoupper(substr($empresa->nombre_comercial, 0, 2)) }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ $empresa->nombre_comercial }}</span>
                                                        <small class="text-muted">{{ $empresa->direccion }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $empresa->ruc }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $empresa->email ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $empresa->telefono ?? 'N/A' }}</span>
                                            </td>
                                            <td class="text-center" >
                                                @if ($empresa->activo)
                                                {{-- tooltip --}}
                                                <span title="Activa" data-bs-toggle="tooltip" data-bs-placement="top" class="badge badge-center rounded-pill bg-label-success bg-glow">
                                                    <i class="ti ti-check"></i>
                                                </span>
                                                @else
                                                <span title="Inactiva" data-bs-toggle="tooltip" data-bs-placement="top" class="badge badge-center rounded-pill bg-label-danger bg-glow">
                                                    <i class="ti ti-circle-x"></i>
                                                </span>
                                                @endif
                                            </td> 
                                            @canany(['editar_empresas', 'eliminar_empresas'])
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-label-primary btn-icon btn-sm rounded dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ti ti-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @can('ver_empresas')
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('grupo.empresas.show', ['grupo' => $grupoActual->slug ?? request()->route('grupo'), 'empresa' => $empresa->id]) }}">
                                                                    <i class="ti ti-list-search me-2"></i> Ver detalles
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('editar_empresas')
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('grupo.empresas.edit', ['grupo' => $grupoActual->slug ?? request()->route('grupo'), 'empresa' => $empresa->id]) }}">
                                                                    <i class="ti ti-edit me-2"></i> Editar
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        <li>
                                                            <hr class="dropdown-divider" />
                                                        </li>
                                                        @can('eliminar_empresas')
                                                            <li>
                                                                <a class="dropdown-item text-warning" href="{{ route('grupo.empresas.update', ['grupo' => $grupoActual->slug ?? request()->route('grupo'), 'empresa' => $empresa->id]) }}">
                                                                    <i class="ti ti-alert-square-rounded me-2"></i> Desactivar
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('eliminar_empresas')
                                                            <li>
                                                                <form action="{{ route('grupo.empresas.destroy', ['grupo' => $grupoActual->slug ?? request()->route('grupo'), 'empresa' => $empresa->id]) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('¿Estás seguro de eliminar esta empresa?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="ti ti-trash me-1"></i> Eliminar
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                            @endcanany
                                                                                       
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    @if(request('buscar') || request('estado') !== null)
                                                        <img src="{{ asset('vuexy/img/illustrations/page-misc-error.png') }}"
                                                            alt="No resultados" width="120" class="mb-3">
                                                        <h6 class="mb-1">No se encontraron empresas</h6>
                                                        <p class="text-muted mb-0">No hay empresas que coincidan con los criterios de búsqueda.</p>
                                                        <a href="{{ route('grupo.empresas.index', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]) }}" class="btn btn-primary mt-2">
                                                            <i class="ti ti-arrow-left me-1"></i>
                                                            Ver todas las empresas
                                                        </a>
                                                    @else
                                                        <img src="{{ asset('vuexy/img/illustrations/page-misc-error.png') }}"
                                                            alt="No empresas" width="120" class="mb-3">
                                                        <h6 class="mb-1">No se encontraron empresas</h6>
                                                        <p class="text-muted mb-0">Comienza creando tu primera empresa en el sistema.</p>
                                                        @can('empresas.create')
                                                            <a href="{{ route('grupo.empresas.create', ['grupo' => $grupoActual->slug ?? request()->route('grupo')]) }}" class="btn btn-primary mt-2">
                                                                <i class="ti ti-plus me-1"></i>
                                                                Crear Primera Empresa
                                                            </a>
                                                        @endcan
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        @if ($empresas->hasPages())
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <div class="text-muted mb-2 mb-sm-0">
                                            <small>
                                                Mostrando {{ $empresas->firstItem() }} a {{ $empresas->lastItem() }} 
                                                de {{ $empresas->total() }} resultados
                                            </small>
                                        </div>
                                        <div>
                                            {{ $empresas->links('components.erp.table-pagination') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row mt-4">
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('vuexy/img/backgrounds/speaker.png') }}" alt="empresas"
                                    class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Empresas</span>
                        <h3 class="card-title mb-2">{{ $totalEmpresas }}</h3>
                        <small class="text-success fw-semibold">
                            <i class="ti ti-up-arrow-alt"></i> Registradas
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('vuexy/img/backgrounds/speaker.png') }}" alt="activas"
                                    class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Empresas Activas</span>
                        <h3 class="card-title mb-2">{{ $empresasActivas }}</h3>
                        <small class="text-info fw-semibold">
                            <i class="ti ti-check-circle"></i> Operativas
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            });

            // Auto-submit form cuando cambia el filtro de estado
            document.getElementById('estado').addEventListener('change', function() {
                this.form.submit();
            });

            // Focus en el campo de búsqueda si hay un error de búsqueda
            @if(request('buscar') && $empresas->count() == 0)
                document.getElementById('buscar').focus();
            @endif
        });
    </script>
@endpush

@push('styles')
    <style>
        .table-responsive {
            border-radius: 0.375rem;
        }
        
        .pagination .page-link {
            border: 1px solid #d9dee3;
            color: #6f6b7d;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #696cff;
            border-color: #696cff;
        }
        
        .pagination .page-link:hover {
            background-color: #f8f9fa;
            border-color: #696cff;
            color: #696cff;
        }

        .form-control:focus, .form-select:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
        }
    </style>
@endpush
