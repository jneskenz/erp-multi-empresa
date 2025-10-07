{{-- 
    Componente de Contexto para Navbar
    
    Muestra el contexto actual (empresa y local) y permite cambio rápido
    
    Uso:
    @include('components.contexto-navbar')
--}}

@if(isset($contextoEmpresa) && $contextoEmpresa)
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" 
           href="#" 
           id="contextoDropdown" 
           role="button" 
           data-bs-toggle="dropdown" 
           aria-expanded="false">
            <div class="d-flex align-items-center">
                <!-- Logo de la empresa (si existe) -->
                @if($contextoEmpresa->logo_url)
                    <img src="{{ $contextoEmpresa->logo_url }}" 
                         alt="{{ $contextoEmpresa->nombre }}" 
                         class="rounded me-2"
                         style="width: 24px; height: 24px; object-fit: cover;">
                @else
                    <i class="fas fa-building me-2"></i>
                @endif
                
                <!-- Información del contexto -->
                <div class="d-none d-md-block">
                    <div class="fw-bold" style="font-size: 0.9rem; line-height: 1.2;">
                        {{ Str::limit($contextoEmpresa->nombre, 20) }}
                    </div>
                    @if(isset($contextoLocal) && $contextoLocal)
                        <div class="text-muted" style="font-size: 0.75rem; line-height: 1;">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ Str::limit($contextoLocal->nombre, 20) }}
                        </div>
                    @endif
                </div>
            </div>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="contextoDropdown" style="min-width: 300px;">
            <!-- Contexto Actual -->
            <li>
                <div class="px-3 py-2 border-bottom">
                    <div class="text-muted small mb-1">Contexto Actual</div>
                    <div class="fw-bold">
                        <i class="fas fa-building text-primary me-2"></i>
                        {{ $contextoEmpresa->nombre }}
                    </div>
                    @if(isset($contextoLocal) && $contextoLocal)
                        <div class="text-muted small mt-1">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            {{ $contextoLocal->nombre }}
                        </div>
                    @endif
                    @if(isset($contextoGrupo))
                        <div class="text-muted small mt-1">
                            <i class="fas fa-layer-group me-2"></i>
                            {{ $contextoGrupo->nombre }}
                        </div>
                    @endif
                </div>
            </li>

            <!-- Cambiar Empresa -->
            @if(isset($empresasConAcceso) && $empresasConAcceso->count() > 1)
                <li><h6 class="dropdown-header">Cambiar Empresa</h6></li>
                @foreach($empresasConAcceso->take(5) as $empresa)
                    @if($empresa->id != $contextoEmpresa->id)
                        <li>
                            <a class="dropdown-item" 
                               href="#"
                               onclick="cambiarEmpresaRapido({{ $empresa->id }}); return false;">
                                <i class="fas fa-building me-2 text-primary"></i>
                                {{ $empresa->nombre }}
                                @if($empresa->id == auth()->user()->empresa_id)
                                    <span class="badge bg-primary ms-2">Principal</span>
                                @endif
                            </a>
                        </li>
                    @endif
                @endforeach
                @if($empresasConAcceso->count() > 5)
                    <li>
                        <a class="dropdown-item text-muted small" href="{{ route('contexto.selector') }}">
                            <i class="fas fa-ellipsis-h me-2"></i>
                            Ver todas ({{ $empresasConAcceso->count() }})
                        </a>
                    </li>
                @endif
                <li><hr class="dropdown-divider"></li>
            @endif

            <!-- Cambiar Local -->
            @if(isset($localesDisponibles) && $localesDisponibles->count() > 0)
                <li><h6 class="dropdown-header">Cambiar Local</h6></li>
                @foreach($localesDisponibles->take(5) as $local)
                    <li>
                        <a class="dropdown-item @if(isset($contextoLocal) && $contextoLocal && $contextoLocal->id == $local->id) active @endif" 
                           href="#"
                           onclick="cambiarLocal({{ $local->id }}); return false;">
                            <i class="fas fa-map-marker-alt me-2 text-secondary"></i>
                            {{ $local->nombre }}
                            @if($local->pivot->es_principal)
                                <span class="badge bg-success ms-2">Principal</span>
                            @endif
                        </a>
                    </li>
                @endforeach
                <li>
                    <a class="dropdown-item" 
                       href="#"
                       onclick="cambiarLocal(null); return false;">
                        <i class="fas fa-times-circle me-2 text-muted"></i>
                        Sin local específico
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
            @endif

            <!-- Acciones -->
            <li>
                <a class="dropdown-item" href="{{ route('contexto.selector') }}">
                    <i class="fas fa-exchange-alt me-2 text-info"></i>
                    Cambiar Contexto Completo
                </a>
            </li>
            
            @if(auth()->user()->esSuperusuario() || auth()->user()->esAdministradorGeneral())
                <li>
                    <a class="dropdown-item" href="{{ route('contexto.limpiar') }}"
                       onclick="event.preventDefault(); document.getElementById('limpiar-contexto-form').submit();">
                        <i class="fas fa-eraser me-2 text-warning"></i>
                        Limpiar Contexto
                    </a>
                </li>
            @endif
        </ul>
    </li>

    {{-- Formulario para limpiar contexto --}}
    <form id="limpiar-contexto-form" action="{{ route('contexto.limpiar') }}" method="POST" class="d-none">
        @csrf
    </form>

    {{-- Formulario para cambio rápido --}}
    <form id="cambio-rapido-form" action="{{ route('contexto.cambio-rapido') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="empresa_id" id="cambio-rapido-empresa-id">
    </form>

    @push('scripts')
    <script>
        function cambiarEmpresaRapido(empresaId) {
            if (confirm('¿Deseas cambiar a esta empresa?')) {
                document.getElementById('cambio-rapido-empresa-id').value = empresaId;
                document.getElementById('cambio-rapido-form').submit();
            }
        }

        function cambiarLocal(localId) {
            const empresaId = {{ $contextoEmpresa->id }};
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("contexto.cambiar") }}';
            
            // CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            // Empresa ID
            const empresaInput = document.createElement('input');
            empresaInput.type = 'hidden';
            empresaInput.name = 'empresa_id';
            empresaInput.value = empresaId;
            form.appendChild(empresaInput);
            
            // Local ID (si existe)
            if (localId) {
                const localInput = document.createElement('input');
                localInput.type = 'hidden';
                localInput.name = 'local_id';
                localInput.value = localId;
                form.appendChild(localInput);
            }
            
            // Redirect to
            const redirectInput = document.createElement('input');
            redirectInput.type = 'hidden';
            redirectInput.name = 'redirect_to';
            redirectInput.value = window.location.href;
            form.appendChild(redirectInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>
    @endpush

@else
    {{-- Si no hay contexto, mostrar botón para seleccionar --}}
    <li class="nav-item">
        <a class="nav-link btn btn-outline-primary btn-sm" href="{{ route('contexto.selector') }}">
            <i class="fas fa-building me-2"></i>
            <span class="d-none d-md-inline">Seleccionar Empresa</span>
        </a>
    </li>
@endif
