@extends('layouts.adm')

@section('title', 'Ver Lead Cliente | ERP Multisoft')

@php
    $breadcrumbs = [
        'title' => 'Ver Lead Cliente',
        'description' => 'Información detallada del lead cliente',
        'icon' => 'ti ti-eye',
        'items' => [
            ['name' => 'CRM', 'url' => 'javascript:void(0)'],
            ['name' => 'Leads Clientes', 'url' => route('admin.lead-cliente.index')],
            ['name' => 'Ver', 'url' => 'javascript:void(0)', 'active' => true],
        ],
    ];
@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-erp.breadcrumbs :items="$breadcrumbs">
            <x-slot:extra>
                @can('lead_cliente.view')
                    <a href="{{ route('admin.lead-cliente.index') }}" class="btn btn-label-dark waves-effect">
                        <i class="ti ti-arrow-left me-2"></i>
                        Regresar
                    </a>
                @endcan
                @can('lead_cliente.edit')
                    <a href="{{ route('admin.lead-cliente.edit', $leadCliente) }}" class="btn btn-primary waves-effect">
                        <i class="ti ti-edit me-2"></i>
                        Editar
                    </a>
                @endcan
            </x-slot:extra>
        </x-erp.breadcrumbs>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">
                                <i class="ti ti-building-bank me-2"></i>
                                Información del Lead Cliente
                            </h5>
                            <small class="text-muted">{{ $leadCliente->empresa }}</small>
                        </div>
                        @if($leadCliente->estado)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Información de la Empresa -->
                            <div class="col-12 mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="ti ti-building me-2"></i>
                                    Información de la Empresa
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Nombre de la Empresa</label>
                                        <p class="fw-semibold">{{ $leadCliente->empresa ?? 'No especificado' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">RUC/NIT</label>
                                        <p class="fw-semibold">{{ $leadCliente->ruc ?? 'No especificado' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Rubro de la Empresa</label>
                                        <p class="fw-semibold">
                                            @if($leadCliente->rubro_empresa)
                                                <span class="badge bg-label-info">{{ $leadCliente->rubro_empresa }}</span>
                                            @else
                                                No especificado
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Número de Empleados</label>
                                        <p class="fw-semibold">{{ $leadCliente->nro_empleados ?? 'No especificado' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">País</label>
                                        <p class="fw-semibold">
                                            @if($leadCliente->pais)
                                                <i class="ti ti-map-pin me-1"></i>{{ $leadCliente->pais }}
                                            @else
                                                No especificado
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Plan de Interés</label>
                                        <p class="fw-semibold">
                                            @if($leadCliente->plan_interes)
                                                @php
                                                    $planBadges = [
                                                        'demo' => 'bg-warning',
                                                        'basico' => 'bg-info',
                                                        'profesional' => 'bg-primary',
                                                        'empresarial' => 'bg-success'
                                                    ];
                                                    $badgeClass = $planBadges[$leadCliente->plan_interes] ?? 'bg-secondary';
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst($leadCliente->plan_interes) }}
                                                </span>
                                            @else
                                                No especificado
                                            @endif
                                        </p>
                                    </div>
                                    @if($leadCliente->descripcion)
                                        <div class="col-12 mb-3">
                                            <label class="form-label text-muted">Descripción del Negocio</label>
                                            <p class="fw-semibold">{{ $leadCliente->descripcion }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Información del Contacto -->
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="ti ti-user me-2"></i>
                                    Información del Contacto
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Nombre del Cliente</label>
                                        <p class="fw-semibold">{{ $leadCliente->cliente ?? 'No especificado' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Documento de Identidad</label>
                                        <p class="fw-semibold">{{ $leadCliente->nro_documento ?? 'No especificado' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Email</label>
                                        <p class="fw-semibold">
                                            @if($leadCliente->correo)
                                                <a href="mailto:{{ $leadCliente->correo }}" class="text-decoration-none">
                                                    <i class="ti ti-mail me-1"></i>{{ $leadCliente->correo }}
                                                </a>
                                            @else
                                                No especificado
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Teléfono</label>
                                        <p class="fw-semibold">
                                            @if($leadCliente->telefono)
                                                <a href="tel:{{ $leadCliente->telefono }}" class="text-decoration-none">
                                                    <i class="ti ti-phone me-1"></i>{{ $leadCliente->telefono }}
                                                </a>
                                            @else
                                                No especificado
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Cargo</label>
                                        <p class="fw-semibold">{{ $leadCliente->cargo ?? 'No especificado' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Información del Sistema -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            Información del Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">ID del Lead</label>
                            <p class="fw-semibold">#{{ $leadCliente->id }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Fecha de Registro</label>
                            <p class="fw-semibold">
                                <i class="ti ti-calendar me-1"></i>
                                {{ $leadCliente->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Última Actualización</label>
                            <p class="fw-semibold">
                                <i class="ti ti-clock me-1"></i>
                                {{ $leadCliente->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <label class="form-label text-muted">Estado</label>
                            <p>
                                @if($leadCliente->estado)
                                    <span class="badge bg-success">
                                        <i class="ti ti-check me-1"></i>Activo
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="ti ti-x me-1"></i>Inactivo
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-settings me-2"></i>
                            Acciones Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @can('lead_cliente.edit')
                                <a href="{{ route('admin.lead-cliente.edit', $leadCliente) }}" class="btn btn-primary">
                                    <i class="ti ti-edit me-2"></i>
                                    Editar Lead
                                </a>
                            @endcan
                            
                            @if($leadCliente->correo)
                                <a href="mailto:{{ $leadCliente->correo }}" class="btn btn-outline-success">
                                    <i class="ti ti-mail me-2"></i>
                                    Enviar Email
                                </a>
                            @endif
                            
                            @if($leadCliente->telefono)
                                <a href="tel:{{ $leadCliente->telefono }}" class="btn btn-outline-info">
                                    <i class="ti ti-phone me-2"></i>
                                    Llamar
                                </a>
                            @endif
                            
                            <a href="{{ route('admin.lead-cliente.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-list me-2"></i>
                                Ver Todos los Leads
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        // Funcionalidad adicional si es necesaria
        document.addEventListener('DOMContentLoaded', function() {
            // Código JavaScript adicional aquí
        });
    </script>
@endsection