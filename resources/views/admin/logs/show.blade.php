@extends('layouts.vuexy')

@section('title', 'Visor de Log: ' . $fileInfo['name'])

@section('page-style')
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}"> --}}
    <style>
        .log-entry {
            margin-bottom: 1rem;
            border-left: 4px solid #e9ecef;
            transition: all 0.2s;
        }

        .log-entry:hover {
            background-color: #f8f9fa;
            border-left-color: #6c757d;
        }

        .log-level-emergency {
            border-left-color: #dc2626 !important;
        }

        .log-level-alert {
            border-left-color: #ea580c !important;
        }

        .log-level-critical {
            border-left-color: #dc2626 !important;
        }

        .log-level-error {
            border-left-color: #ef4444 !important;
        }

        .log-level-warning {
            border-left-color: #f59e0b !important;
        }

        .log-level-notice {
            border-left-color: #3b82f6 !important;
        }

        .log-level-info {
            border-left-color: #10b981 !important;
        }

        .log-level-debug {
            border-left-color: #6b7280 !important;
        }

        .log-level-badge {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            padding: 0.25rem 0.5rem;
        }

        .log-timestamp {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .log-message {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .log-context {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.75rem;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-top: 0.5rem;
            max-height: 200px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .log-filters {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .file-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .sticky-filters {
            position: sticky;
            top: 80px;
            z-index: 1020;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: -1rem -1.5rem 1.5rem -1.5rem;
            padding: 1rem 1.5rem;
        }

        .log-viewer-container {
            max-height: calc(100vh - 300px);
            overflow-y: auto;
        }

        .log-entry-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .log-entry:nth-child(odd) {
            background-color: rgba(0, 0, 0, 0.01);
        }

        .log-message {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin: 0.5rem 0;
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
            background-color: rgba(67, 89, 113, 0.04);
        }

        .log-message-preview {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Manejo de mensajes de sesión -->
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    toastr.success('{{ session('success') }}', 'Éxito');
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    toastr.error('{{ session('error') }}', 'Error');
                });
            </script>
        @endif

        <!-- Header con información del archivo -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card file-info-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="card-titl mb-2">
                                    <i class="ti ti-file-text me-2"></i>
                                    {{ $fileInfo['name'] }}
                                </h4>
                                <div class="d-flex flex-wrap gap-3">
                                    <span><i class="ti ti-database me-1"></i>{{ $fileInfo['size'] }}</span>
                                    <span><i
                                            class="ti ti-clock me-1"></i>{{ $fileInfo['modified']->format('d/m/Y H:i:s') }}</span>
                                    <span><i
                                            class="ti ti-calendar me-1"></i>{{ $fileInfo['modified']->diffForHumans() }}</span>
                                    <span><i class="ti ti-list-numbers me-1"></i>{{ count($logEntries) }} entradas</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-light btn-sm">
                                    <i class="ti ti-arrow-left me-1"></i>Volver
                                </a>
                                <a href="{{ route('admin.logs.download', $fileInfo['name']) }}"
                                    class="btn btn-light btn-sm">
                                    <i class="ti ti-download me-1"></i>Descargar
                                </a>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="testButton()">
                                    <i class="ti ti-test-pipe me-1"></i>Test
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                    onclick="confirmDeleteFile('{{ $fileInfo['name'] }}')">
                                    <i class="ti ti-trash me-1"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <!-- Filtros -->
                <div class="card">
                <div class="card-body">
                    <div class="sticky-filters">
                        <form method="GET" class="row g-3" id="filterForm">
                            <div class="col-md-2">
                                <label for="lines" class="form-label">
                                    <i class="ti ti-list-numbers me-1"></i>Líneas
                                </label>
                                <select class="form-select" name="lines" id="lines">
                                    <option value="50" {{ $lines == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $lines == 100 ? 'selected' : '' }}>100</option>
                                    <option value="200" {{ $lines == 200 ? 'selected' : '' }}>200</option>
                                    <option value="500" {{ $lines == 500 ? 'selected' : '' }}>500</option>
                                    <option value="1000" {{ $lines == 1000 ? 'selected' : '' }}>1000</option>
                                    <option value="5000" {{ $lines == 5000 ? 'selected' : '' }}>5000 (Recomendado)</option>
                                    <option value="10000" {{ $lines == 10000 ? 'selected' : '' }}>10000</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="level" class="form-label">
                                    <i class="ti ti-filter me-1"></i>Nivel
                                </label>
                                <select class="form-select" name="level" id="level">
                                    <option value="">Todos</option>
                                    <option value="emergency" {{ $level == 'emergency' ? 'selected' : '' }}>Emergency
                                    </option>
                                    <option value="alert" {{ $level == 'alert' ? 'selected' : '' }}>Alert</option>
                                    <option value="critical" {{ $level == 'critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="error" {{ $level == 'error' ? 'selected' : '' }}>Error</option>
                                    <option value="warning" {{ $level == 'warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="notice" {{ $level == 'notice' ? 'selected' : '' }}>Notice</option>
                                    <option value="info" {{ $level == 'info' ? 'selected' : '' }}>Info</option>
                                    <option value="debug" {{ $level == 'debug' ? 'selected' : '' }}>Debug</option>
                                </select>
                            </div>

                            <div class="col-md-5">
                                <label for="search" class="form-label">
                                    <i class="ti ti-search me-1"></i>Buscar en contenido
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" id="search"
                                        value="{{ $search }}" placeholder="Buscar texto en los logs...">
                                    @if ($search)
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-filter"></i>
                                </button>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('admin.logs.show', $fileInfo['name']) }}"
                                    class="btn btn-outline-secondary w-100">
                                    <i class="ti ti-refresh me-1"></i>Limpiar
                                </a>
                            </div>
                        </form>

                        @if (count($logEntries) > 0)
                            <div class="mt-3 text-muted small">
                                <i class="ti ti-info-circle me-1"></i>
                                Mostrando {{ count($logEntries) }} entradas
                                @if ($search || $level)
                                    @if ($search)
                                        con búsqueda "{{ $search }}"
                                    @endif
                                    @if ($level)
                                        filtrado por "{{ strtoupper($level) }}"
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Contenido del Log -->
                    @if (count($logEntries) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover" id="logTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">#</th>
                                        <th width="100">NIVEL</th>
                                        <th width="180">TIMESTAMP</th>
                                        <th width="80">ENV</th>
                                        <th>MENSAJE</th>
                                        <th width="120" class="text-center">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($logEntries as $index => $entry)
                                        <tr class="log-level-{{ strtolower($entry['level']) }}">
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="badge bg-label-primary rounded-circle"
                                                        style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                                        {{ $index + 1 }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge log-level-badge 
                                        @switch(strtolower($entry['level']))
                                            @case('emergency')
                                            @case('alert')
                                            @case('critical')
                                            @case('error')
                                                bg-danger
                                                @break
                                            @case('warning')
                                                bg-warning
                                                @break
                                            @case('notice')
                                            @case('info')
                                                bg-info
                                                @break
                                            @case('debug')
                                                bg-secondary
                                                @break
                                            @default
                                                bg-light text-dark
                                        @endswitch
                                    ">
                                                    {{ $entry['level'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <span
                                                        class="fw-medium">{{ \Carbon\Carbon::parse($entry['timestamp'])->format('d/m/Y') }}</span>
                                                    <br>
                                                    <small
                                                        class="text-muted log-timestamp">{{ \Carbon\Carbon::parse($entry['timestamp'])->format('H:i:s') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-secondary">{{ $entry['environment'] }}</span>
                                            </td>
                                            <td>
                                                <div class="log-message-preview" style="max-width: 400px;">
                                                    <span class="text-truncate d-block" title="{{ $entry['message'] }}">
                                                        {{ \Str::limit($entry['message'], 100) }}
                                                    </span>
                                                    @if (!empty(trim($entry['context'])))
                                                        <small class="text-muted">
                                                            <i class="ti ti-code me-1"></i>Incluye contexto
                                                            ({{ strlen(trim($entry['context'])) }} chars)
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#logModal{{ $index }}"
                                                        title="Ver detalles">
                                                        <i class="ti ti-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        onclick="copyToClipboard('log-message-{{ $index }}')"
                                                        title="Copiar mensaje">
                                                        <i class="ti ti-copy"></i>
                                                    </button>
                                                </div>
                                                <div style="display: none;" id="log-message-{{ $index }}">
                                                    {{ $entry['message'] }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="card-body text-center py-5">
                            <div class="mb-3">
                                <i class="ti ti-search-off display-4 text-muted"></i>
                            </div>
                            <h5 class="mb-2">No se encontraron entradas</h5>
                            <p class="text-muted">
                                @if ($search || $level)
                                    No hay entradas que coincidan con los filtros aplicados.
                                    <br>
                                    <a href="{{ route('admin.logs.show', $fileInfo['name']) }}"
                                        class="btn btn-outline-primary btn-sm mt-2">
                                        <i class="ti ti-refresh me-1"></i>Limpiar Filtros
                                    </a>
                                @else
                                    El archivo de log está vacío o no contiene entradas válidas.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales para mostrar detalles de cada entrada -->
    @foreach ($logEntries as $index => $entry)
        <div class="modal fade" id="logModal{{ $index }}" tabindex="-1"
            aria-labelledby="logModalLabel{{ $index }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logModalLabel{{ $index }}">
                            <i class="ti ti-file-text me-2"></i>
                            Detalle de Entrada #{{ $index + 1 }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Header con información -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Nivel</h6>
                                <span
                                    class="badge log-level-badge 
                                @switch(strtolower($entry['level']))
                                    @case('emergency')
                                    @case('alert')
                                    @case('critical')
                                    @case('error')
                                        bg-danger
                                        @break
                                    @case('warning')
                                        bg-warning
                                        @break
                                    @case('notice')
                                    @case('info')
                                        bg-info
                                        @break
                                    @case('debug')
                                        bg-secondary
                                        @break
                                    @default
                                        bg-light text-dark
                                @endswitch
                            ">
                                    {{ $entry['level'] }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Entorno</h6>
                                <span class="badge bg-label-secondary">{{ $entry['environment'] }}</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-muted mb-1">Timestamp</h6>
                                <span class="log-timestamp">{{ $entry['timestamp'] }}</span>
                            </div>
                        </div>

                        <!-- Mensaje -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Mensaje</h6>
                            <div class="log-message"
                                style="background-color: #f8f9fa; border-radius: 0.375rem; padding: 1rem; font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace; font-size: 0.875rem; line-height: 1.5; white-space: pre-wrap; word-break: break-word;">
                                {{ $entry['message'] }}</div>
                        </div>

                        <!-- Contexto/Stack trace si existe -->
                        @if (!empty(trim($entry['context'])))
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">
                                    <i class="ti ti-code me-1"></i>Contexto/Stack Trace
                                    <span class="badge bg-info ms-1">{{ strlen(trim($entry['context'])) }} chars</span>
                                </h6>
                                <div class="log-context"
                                    style="background-color: #f8f9fa; border-radius: 0.375rem; padding: 1rem; font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace; font-size: 0.75rem; color: #6c757d; white-space: pre-wrap; word-break: break-word; max-height: 300px; overflow-y: auto;">
                                    {{ trim($entry['context']) }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="copyToClipboard('log-message-{{ $index }}')">
                            <i class="ti ti-copy me-1"></i>Copiar Mensaje
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Floating Action Button para volver arriba -->
    <div class="floating-action" id="scrollToTop" style="display: none;">
        <button type="button" class="btn btn-primary rounded-circle p-3">
            <i class="ti ti-arrow-up"></i>
        </button>
    </div>
@endsection

@section('page-script')

    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script>
        // Función para copiar al portapapeles
        function copyToClipboard(elementId) {
            console.log('copyToClipboard llamada con:', elementId); // Debug
            
            const element = document.getElementById(elementId);
            if (!element) {
                console.error('Elemento no encontrado:', elementId);
                return;
            }
            
            const text = element.textContent;

            navigator.clipboard.writeText(text).then(function() {
                alert('Copiado al portapapeles');
            }, function() {
                // Fallback para navegadores más antiguos
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Copiado al portapapeles');
            });
        }

        // Scroll to top functionality
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('#scrollToTop').fadeIn();
            } else {
                $('#scrollToTop').fadeOut();
            }
        });

        $('#scrollToTop').click(function() {
            $('html, body').animate({
                scrollTop: 0
            }, 800);
            return false;
        });

        // Auto-submit form cuando cambian los filtros
        $('#lines, #level').change(function() {
            $(this).closest('form').submit();
        });

        // Highlight search terms
        @if ($search)
            $(document).ready(function() {
                const searchTerm = '{{ addslashes($search) }}';
                if (searchTerm) {
                    $('.log-message').each(function() {
                        const content = $(this).html();
                        const highlighted = content.replace(
                            new RegExp(searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi'),
                            '<mark>$&</mark>'
                        );
                        $(this).html(highlighted);
                    });
                }
            });
        @endif

        // Atajos de teclado
        $(document).keydown(function(e) {
            // Ctrl + F para focus en búsqueda
            if (e.ctrlKey && e.keyCode === 70) {
                e.preventDefault();
                $('#search').focus();
            }

            // Escape para limpiar búsqueda
            if (e.keyCode === 27) {
                $('#search').val('');
            }
        });

        // Función para limpiar búsqueda
        function clearSearch() {
            document.getElementById('search').value = '';
            document.getElementById('filterForm').submit();
        }

        // Función para confirmar eliminación de archivo
        function confirmDeleteFile(filename) {
            console.log('confirmDeleteFile llamada con:', filename); // Debug
            
            if (confirm('¿Estás seguro de que deseas eliminar el archivo: ' + filename + '?\n\nEsta acción no se puede deshacer.')) {
                console.log('Usuario confirmó eliminación'); // Debug
                
                // Crear form dinámico para eliminar
                const form = document.createElement('form');
                form.method = 'POST';
                
                const actionUrl = '{{ route('admin.logs.delete', ':filename') }}'.replace(':filename', filename);
                console.log('URL de acción:', actionUrl); // Debug
                form.action = actionUrl;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            } else {
                console.log('Usuario canceló eliminación'); // Debug
            }
        }

        // Función para confirmar eliminación (para compatibilidad con vista index)
        function confirmDelete(filename) {
            console.log('confirmDelete llamada con:', filename); // Debug
            
            if (confirm('¿Estás seguro de que deseas eliminar el archivo: ' + filename + '?\n\nEsta acción no se puede deshacer.')) {
                console.log('Usuario confirmó eliminación'); // Debug
                
                // Crear form dinámico para eliminar
                const form = document.createElement('form');
                form.method = 'POST';
                
                const actionUrl = '{{ route('admin.logs.delete', ':filename') }}'.replace(':filename', filename);
                console.log('URL de acción:', actionUrl); // Debug
                form.action = actionUrl;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            } else {
                console.log('Usuario canceló eliminación'); // Debug
            }
        }

        // Función de test (para debugging)
        function testButton() {
            alert('¡Test button en vista show funciona!');
            console.log('Test button en show clicked');
        }

        // Inicializar DataTable
        $(document).ready(function() {
            $('#logTable').DataTable({
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Todos"]
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                responsive: true,
                order: [
                    [0, 'asc']
                ], // Ordenar por número de entrada
                columnDefs: [{
                        targets: [5], // Columna de acciones
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: [0], // Columna de número
                        width: "60px"
                    },
                    {
                        targets: [1], // Columna de nivel
                        width: "100px"
                    },
                    {
                        targets: [2], // Columna de timestamp
                        width: "180px"
                    },
                    {
                        targets: [3], // Columna de entorno
                        width: "80px"
                    },
                    {
                        targets: [5], // Columna de acciones
                        width: "120px"
                    }
                ]
            });
        });
    </script>

    <style>
        .floating-action {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        mark {
            background-color: yellow;
            padding: 0.1em 0.2em;
            border-radius: 0.2em;
        }
    </style>
@endsection
