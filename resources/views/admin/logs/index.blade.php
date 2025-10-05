@extends('layouts.vuexy')

@section('title', 'Gestión de Logs del Sistema')

@section('page-style')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        .log-level-badge {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .log-level-emergency { background-color: #dc2626 !important; }
        .log-level-alert { background-color: #ea580c !important; }
        .log-level-critical { background-color: #dc2626 !important; }
        .log-level-error { background-color: #ef4444 !important; }
        .log-level-warning { background-color: #f59e0b !important; }
        .log-level-notice { background-color: #3b82f6 !important; }
        .log-level-info { background-color: #10b981 !important; }
        .log-level-debug { background-color: #6b7280 !important; }
        
        .log-stats-card {
            transition: transform 0.2s;
        }
        .log-stats-card:hover {
            transform: translateY(-2px);
        }
        
        .file-size-badge {
            font-size: 0.7rem;
        }
        
        .btn-group .btn {
            border-radius: 0.375rem !important;
            margin-right: 0.25rem;
        }
        
        .btn-group .btn:last-child {
            margin-right: 0;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            font-size: 0.875rem;
            color: #5a6169;
        }
        
        .table tbody tr:hover {
            background-color: rgba(67, 89, 113, 0.05);
        }
    </style>

{{-- @endsection

@section('page-script') --}}

    <script>        

        // Inicializar DataTable
        $(document).ready(function() {
            
            if ($.fn.DataTable) {
                $('#logsTable').DataTable({
                    responsive: true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                    },
                    order: [[2, 'desc']], // Ordenar por fecha de modificación
                    pageLength: 25,
                    columnDefs: [
                        { orderable: false, targets: [4] } // Columna de acciones no ordenable
                    ]
                });
            }

            // Auto-refresh cada 30 segundos
            setInterval(refreshStats, 30000);
        });

    </script>

@endsection

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('success'))
            <script>
                alert('✓ {{ session('success') }}');
            </script>
        @endif
        @if (session('error'))
            <script>
                alert('✗ {{ session('error') }}');
            </script>
        @endif

        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="ti ti-bug me-2 text-danger"></i>
                                Gestión de Logs del Sistema
                            </h4>
                            <p class="text-muted mb-0">Monitoreo y gestión de archivos de logs del sistema</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.logs.dashboard') }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-dashboard me-1"></i>
                                Dashboard
                            </a>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="refreshStats()">
                                <i class="ti ti-refresh me-1"></i>
                                Actualizar
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="testButton()">
                                <i class="ti ti-test-pipe me-1"></i>
                                Test
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#cleanLogsModal">
                                <i class="ti ti-trash me-1"></i>
                                Limpiar Logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4" id="logStats">
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card log-stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text text-muted">Total Archivos</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="card-title mb-0 me-2" id="totalFiles">{{ count($logFiles) }}</h4>
                                    <small class="text-success">archivos</small>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-primary rounded p-2">
                                    <i class="ti ti-files ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card log-stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text text-muted">Tamaño Total</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="card-title mb-0 me-2" id="totalSize">
                                        {{ array_sum(array_column($logFiles, 'size_bytes')) > 0 ? number_format(array_sum(array_column($logFiles, 'size_bytes'))/1024/1024, 2) . ' MB' : '0 B' }}
                                    </h4>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-info rounded p-2">
                                    <i class="ti ti-database ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card log-stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text text-muted">Errores Hoy</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="card-title mb-0 me-2" id="errorsToday">0</h4>
                                    <small class="text-danger">eventos</small>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-danger rounded p-2">
                                    <i class="ti ti-alert-triangle ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card log-stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text text-muted">Último Archivo</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h6 class="card-title mb-0 me-2" id="latestFile">
                                        @if(count($logFiles) > 0)
                                            {{ $logFiles[0]['modified']->format('H:i') }}
                                        @else
                                            Sin archivos
                                        @endif
                                    </h6>
                                    <small class="text-info">
                                        @if(count($logFiles) > 0)
                                            {{ $logFiles[0]['modified']->format('d/m') }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-success rounded p-2">
                                    <i class="ti ti-clock ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Archivos de Log -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ti ti-list me-2"></i>
                    Archivos de Log
                </h5>
                <span class="badge bg-primary">{{ count($logFiles) }} archivos</span>
            </div>

            @if(count($logFiles) > 0)
                <div class="table-responsive">
                    <table class="table table-hover" id="logsTable">
                        <thead class="table-light">
                            <tr>
                                <th><i class="ti ti-file me-1"></i>Archivo</th>
                                <th><i class="ti ti-database me-1"></i>Tamaño</th>
                                <th><i class="ti ti-calendar me-1"></i>Última Modificación</th>
                                <th><i class="ti ti-shield-check me-1"></i>Estado</th>
                                <th class="text-center"><i class="ti ti-settings me-1"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logFiles as $logFile)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-file-text me-2 text-primary"></i>
                                            <div>
                                                <span class="fw-medium">{{ $logFile['name'] }}</span>
                                                @if(str_contains($logFile['name'], 'laravel'))
                                                    <span class="badge bg-label-primary ms-2 file-size-badge">Principal</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info file-size-badge">{{ $logFile['size'] }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-medium">{{ $logFile['modified']->format('d/m/Y H:i:s') }}</span>
                                            <br>
                                            <small class="text-muted">{{ $logFile['modified']->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($logFile['is_readable'])
                                            <span class="badge bg-success">
                                                <i class="ti ti-check me-1"></i>Legible
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="ti ti-x me-1"></i>No Legible
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @if($logFile['is_readable'])
                                                <a href="{{ route('admin.logs.show', $logFile['name']) }}" 
                                                class="btn btn-sm btn-outline-primary" 
                                                title="Ver contenido">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.logs.download', $logFile['name']) }}" 
                                                class="btn btn-sm btn-outline-info" 
                                                title="Descargar">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                            @endif
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete('{{ $logFile['name'] }}')"
                                                    title="Eliminar">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="ti ti-file-x display-4 text-muted"></i>
                    </div>
                    <h5 class="mb-2">No se encontraron archivos de log</h5>
                    <p class="text-muted">No hay archivos de log disponibles en el directorio storage/logs</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Limpiar Logs -->
    <div class="modal fade" id="cleanLogsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-trash me-2 text-warning"></i>
                        Limpiar Logs Antiguos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.logs.clean') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="ti ti-alert-triangle me-2"></i>
                            Esta acción eliminará permanentemente los archivos de log antiguos.
                        </div>
                        
                        <div class="mb-3">
                            <label for="days" class="form-label">Eliminar logs más antiguos de:</label>
                            <select class="form-select" name="days" id="days">
                                <option value="7">7 días</option>
                                <option value="15">15 días</option>
                                <option value="30">30 días</option>
                                <option value="60">60 días</option>
                                <option value="90">90 días</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="ti ti-trash me-2"></i>Limpiar Logs
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form oculto para eliminar archivos -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

@endsection

