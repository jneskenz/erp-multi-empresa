@extends('layouts.vuexy')

@section('title', 'Panel de Monitoreo de Logs')

@section('page-style')
<style>
    .log-alert-card {
        border-left: 4px solid #dc3545;
        background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
    }
    
    .critical-errors {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .log-entry-mini {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.75rem;
        padding: 0.5rem;
        border-radius: 0.25rem;
        background-color: #f8f9fa;
        border-left: 3px solid #dc3545;
        margin-bottom: 0.5rem;
    }
    
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Alert para errores críticos -->
    <div id="criticalErrorsAlert" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle me-2 pulse-animation"></i>
            <div>
                <strong>¡Errores críticos detectados!</strong>
                <span id="criticalErrorsCount">0</span> errores críticos en las últimas 24 horas.
                <a href="{{ route('admin.logs.index') }}" class="alert-link">Ver detalles</a>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Dashboard de Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ti ti-dashboard me-2"></i>
                        Panel de Monitoreo de Logs en Tiempo Real
                    </h4>
                    <p class="text-muted mb-0">Monitoreo automático del estado del sistema</p>
                </div>
                <div class="card-body">
                    <div class="row" id="realTimeStats">
                        <!-- Stats se cargarán dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos errores críticos -->
    <div class="card log-alert-card" id="recentErrorsCard" style="display: none;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-danger">
                <i class="ti ti-bug me-2"></i>
                Últimos Errores Críticos
            </h5>
            <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-danger btn-sm">
                Ver Todos los Logs
            </a>
        </div>
        <div class="card-body">
            <div class="critical-errors" id="recentErrorsList">
                <!-- Se cargarán dinámicamente -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
    // Función para cargar estadísticas en tiempo real
    function loadRealTimeStats() {
        fetch('{{ route('admin.logs.stats') }}')
            .then(response => response.json())
            .then(data => {
                updateStatsDisplay(data);
                updateCriticalErrorsAlert(data);
            })
            .catch(error => {
                console.error('Error loading stats:', error);
            });
    }

    // Función para actualizar la visualización de estadísticas
    function updateStatsDisplay(data) {
        const statsContainer = document.getElementById('realTimeStats');
        statsContainer.innerHTML = `
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">${data.total_files}</h4>
                                <p class="text-muted mb-0">Archivos de Log</p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti ti-files"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">${data.total_size_formatted}</h4>
                                <p class="text-muted mb-0">Tamaño Total</p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="ti ti-database"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1 ${data.error_count_today > 0 ? 'text-danger' : 'text-success'}">${data.error_count_today}</h4>
                                <p class="text-muted mb-0">Errores Hoy</p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded ${data.error_count_today > 0 ? 'bg-label-danger' : 'bg-label-success'}">
                                    <i class="ti ${data.error_count_today > 0 ? 'ti-alert-triangle' : 'ti-check'}"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Estado</h6>
                                <p class="text-muted mb-0 ${data.error_count_today === 0 ? 'text-success' : data.error_count_today < 10 ? 'text-warning' : 'text-danger'}">
                                    ${data.error_count_today === 0 ? 'Sistema Estable' : data.error_count_today < 10 ? 'Atención Requerida' : 'Estado Crítico'}
                                </p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded ${data.error_count_today === 0 ? 'bg-label-success' : data.error_count_today < 10 ? 'bg-label-warning' : 'bg-label-danger'}">
                                    <i class="ti ${data.error_count_today === 0 ? 'ti-shield-check' : data.error_count_today < 10 ? 'ti-shield' : 'ti-shield-x'}"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Función para actualizar alerta de errores críticos
    function updateCriticalErrorsAlert(data) {
        const alertElement = document.getElementById('criticalErrorsAlert');
        const countElement = document.getElementById('criticalErrorsCount');
        const recentErrorsCard = document.getElementById('recentErrorsCard');

        if (data.error_count_today > 5) { // Mostrar alerta si hay más de 5 errores
            countElement.textContent = data.error_count_today;
            alertElement.classList.remove('d-none');
            recentErrorsCard.style.display = 'block';
            
            // Mostrar último error si existe
            if (data.latest_error) {
                const errorsList = document.getElementById('recentErrorsList');
                errorsList.innerHTML = `
                    <div class="log-entry-mini">
                        <strong>Último error detectado:</strong><br>
                        ${data.latest_error}
                    </div>
                `;
            }
        } else {
            alertElement.classList.add('d-none');
            recentErrorsCard.style.display = 'none';
        }
    }

    // Cargar estadísticas al cargar la página
    $(document).ready(function() {
        loadRealTimeStats();
        
        // Actualizar cada 15 segundos
        setInterval(loadRealTimeStats, 15000);
        
        // Mostrar toast de bienvenida
        toastr.success('Panel de monitoreo iniciado', 'Sistema de Logs');
    });
</script>
@endsection