@extends('layouts.adm')

@section('title', 'BACKOFFICE - Leads Clientes | ERP Multisoft')

@php
    $breadcrumbs = [
        'title' => 'Leads Clientes',
        'description' => 'Administración de leads clientes del sistema',
        'icon' => 'ti ti-users',
        'items' => [
            ['name' => 'CRM', 'url' => 'javascript:void(0)'],
            ['name' => 'Leads Clientes', 'url' => 'javascript:void(0)', 'active' => true],
        ],
    ];
@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <x-erp.breadcrumbs :items="$breadcrumbs">
            <x-slot:acciones>
                @can('lead_cliente.create')
                    <a href="{{ route('admin.lead-cliente.create') }}" class="btn btn-primary waves-effect">
                        <i class="ti ti-plus me-2"></i>
                        Nuevo Lead Cliente
                    </a>
                @endcan
            </x-slot:acciones>
        </x-erp.breadcrumbs>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ti ti-building-bank me-2"></i>
                    Lista de Leads Clientes
                </h5>
                <span class="badge bg-primary">{{ $leads->total() }} leads total</span>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-check me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-x me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Filtros y Búsqueda -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <!-- Indicador de filtros activos -->
                        

                        <form method="GET" action="{{ route('admin.lead-cliente.index') }}" class="row g-3">
                            <div class="col-md-1">
                                <select class="form-select" id="per_page" name="per_page">
                                    <option value="5" {{ request('per_page') == '5' ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ request('per_page', '10') == '10' ? 'selected' : '' }}>10</option>
                                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                {{-- <label for="search" class="form-label">Buscar</label> --}}
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, código, descripción...">
                                    <button type="submit" class="input-group-text btn btn-outline-secondary">
                                        <i class="ti ti-search me-2"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-2 text-center">
                                <div class="d-flex gap-2">
                                    {{-- <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-search me-2"></i>Buscar
                                    </button> --}}
                                    <a href="{{ route('admin.lead-cliente.index') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-refresh me-2"></i>Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @if ($leads->count() > 0)
                    <!-- Información de paginación superior -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>EMPRESA</th>
                                    <th>RUBRO</th>
                                    <th>EMPLEADOS</th>
                                    <th>PAIS</th>
                                    <th>DESCRIPCION</th>
                                    <th>CLIENTE</th>
                                    <th>CONTACTO</th>
                                    <th>CARGO</th>
                                    <th>PLAN</th>
                                    <th>ESTADO</th>
                                    <th width="100">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leads as $lead)
                                    <tr>
                                        <td>
                                            <small>{{ Str::limit($lead->empresa, 10) }}</small>
                                            <span
                                                class="badge bg-label-secondary">{{ $lead->ruc ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $lead->rubro_empresa ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-label-info">{{ $lead->nro_empleados }} </span>
                                            Empleado(s)
                                        </td>
                                        <td>{{ $lead->pais ?? '-' }}</td>
                                        <td>
                                            {{ $lead->descripcion ?? '-' }}
                                        </td>
                                        <td>
                                            <strong>{{ $lead->cliente }}</strong><br>
                                            <small class="text-muted">{{ $lead->nro_documento ?? 'Sin documento' }}</small>
                                        </td>
                                        <td class="text-center" >
                                            <span title="{{ $lead->telefono ?? '-' }} | {{ $lead->correo ?? '-' }}">
                                            {{ $lead->telefono ?? '-' }}<br>
                                            {{ Str::limit($lead->correo, 10) ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $lead->cargo ?? '-' }}
                                        </td>
                                        <td>
                                            @if($lead->plan_interes == 'demo')
                                                <span class="badge bg-warning">Demo</span>
                                            @elseif($lead->plan_interes == 'basico')
                                                <span class="badge bg-info">Básico</span>
                                            @elseif($lead->plan_interes == 'profesional')
                                                <span class="badge bg-primary">Profesional</span>
                                            @elseif($lead->plan_interes == 'empresarial')
                                                <span class="badge bg-success">Empresarial</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $lead->plan_interes ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($lead->estado)
                                                <span class="badge bg-success">
                                                    <i class="ti ti-check me-1"></i>Activo
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="ti ti-x me-1"></i>Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button"
                                                    class="btn btn-label-primary btn-icon btn-sm rounded dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @can('lead_cliente.view')
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.lead-cliente.show', $lead) }}">
                                                                <i class="ti ti-eye me-2"></i> Ver detalles
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('lead_cliente.edit')
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.lead-cliente.edit', $lead) }}">
                                                                <i class="ti ti-edit me-2"></i> Editar
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    <li>
                                                        <hr class="dropdown-divider" />
                                                    </li>
                                                    @can('lead_cliente.create')
                                                        <li>
                                                            <button type="button" class="dropdown-item text-success" 
                                                                    onclick="confirmarDarDeAlta({{ $lead->id }}, '{{ $lead->empresa }}', '{{ $lead->cliente }}')">
                                                                <i class="ti ti-user-check me-2"></i>Dar de Alta
                                                            </button>
                                                        </li>
                                                    @endcan
                                                    @can('lead_cliente.delete')
                                                        <li>
                                                            <button type="button" class="dropdown-item text-danger" 
                                                                    onclick="confirmarEliminacion({{ $lead->id }}, '{{ $lead->empresa }}')">
                                                                <i class="ti ti-trash me-2"></i>Eliminar
                                                            </button>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    

                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="pagination-info">
                            Mostrando {{ $leads->firstItem() }} - {{ $leads->lastItem() }} de {{ $leads->total() }} resultados
                        </div>
                        <div>
                            {{ $leads->links('components.erp.table-pagination') }}
                        </div>
                    </div>

                @else
                    <div class="text-center py-5">
                        @if(request('search') || request('estado') !== null)
                            <i class="ti ti-search-off display-4 text-muted"></i>
                            <h4 class="mt-3">No se encontraron resultados</h4>
                            <p class="text-muted">Intente modificar los criterios de búsqueda</p>
                            <a href="{{ route('admin.grupo-empresarial.index') }}" class="btn btn-outline-primary">
                                <i class="ti ti-refresh me-2"></i>Ver todos los grupos
                            </a>
                        @else
                            <i class="ti ti-building-bank display-4 text-muted"></i>
                            <h4 class="mt-3">No hay grupos empresariales registrados</h4>
                            <p class="text-muted">Comience creando su primer leads</p>
                            @can('grupo_empresarial.create')
                                <a href="{{ route('admin.grupo-empresarial.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-2"></i>Crear un nuevo Leads
                                </a>
                            @endcan
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarDarDeAlta(leadId, empresaName, clienteName) {
            Swal.fire({
                title: '¿Dar de Alta Lead Cliente?',
                html: `
                    <div class="text-start">
                        <p>¿Está seguro de dar de alta el lead?</p>
                        <ul class="list-unstyled mt-3">
                            <li><strong>Empresa:</strong> ${empresaName}</li>
                            <li><strong>Cliente:</strong> ${clienteName}</li>
                        </ul>
                        <div class="alert alert-info mt-3">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Esto creará:</strong>
                            <ul class="mb-0 mt-2">
                                <li>✓ Un grupo empresarial</li>
                                <li>✓ Un usuario de acceso</li>
                                <li>✓ Asignación de rol según plan</li>
                            </ul>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, dar de alta',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Creando grupo empresarial y usuario',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    // Crear y enviar formulario
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/lead-cliente/${leadId}/dar-de-alta`;
                    
                    // Token CSRF
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    document.body.appendChild(form);
                    
                    // Enviar con fetch para manejar la respuesta
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Éxito!',
                                html: `
                                    <div class="text-start">
                                        <p>${data.message}</p>
                                        <div class="alert alert-success mt-3">
                                            <strong>Detalles:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li><strong>Empresa:</strong> ${data.data.empresa}</li>
                                                <li><strong>Usuario:</strong> ${data.data.usuario_email}</li>
                                                <li><strong>Rol:</strong> ${data.data.rol_asignado}</li>
                                                <li><strong>Contraseña:</strong> ${data.data.password}</li>
                                            </ul>
                                        </div>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: 'Entendido',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'Entendido',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al procesar la solicitud',
                            icon: 'error',
                            confirmButtonText: 'Entendido',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    })
                    .finally(() => {
                        document.body.removeChild(form);
                    });
                }
            });
        }

        function confirmarEliminacion(leadId, empresaName) {
            Swal.fire({
                title: '¿Eliminar Lead Cliente?',
                html: `¿Está seguro de eliminar el lead de <strong>${empresaName}</strong>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Crear y enviar formulario de eliminación
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/lead-cliente/${leadId}`;
                    
                    // Token CSRF
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    // Method spoofing para DELETE
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Auto-submit del formulario de búsqueda al cambiar elementos por página
        document.getElementById('per_page').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
@endsection