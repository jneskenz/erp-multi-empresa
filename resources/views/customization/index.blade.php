@extends('layouts.vuexy')

@section('title', 'Personalización del Sistema')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb Component -->
    @include('layouts.vuexy.breadcrumb', $dataBreadcrumb)

    <div class="row">
        <div class="col-12">
            <div class="card">
                @include('layouts.vuexy.header-card', $dataHeaderCard)

                <div class="card-body">
                    <!-- Mensajes de estado -->
                    <div id="customization-alerts" class="mb-3"></div>

                    <form id="customization-form" method="POST" action="{{ route('customization.update') }}">
                        @csrf
                        <div class="row">
                            <!-- Configuración de Tema -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="ti ti-sun me-2"></i>
                                            Configuración de Tema
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Modo de tema -->
                                        <div class="mb-3">
                                            <label class="form-label">Modo de Tema</label>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="theme_light">
                                                            <span class="custom-option-header pb-0">
                                                                <input name="theme_mode" class="form-check-input" type="radio" value="light" id="theme_light" {{ $customization->theme_mode == 'light' ? 'checked' : '' }}>
                                                                <span class="fw-medium">Claro</span>
                                                                <i class="ti ti-sun fs-2"></i>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="theme_dark">
                                                            <span class="custom-option-header pb-0">
                                                                <input name="theme_mode" class="form-check-input" type="radio" value="dark" id="theme_dark" {{ $customization->theme_mode == 'dark' ? 'checked' : '' }}>
                                                                <span class="fw-medium">Oscuro</span>
                                                                <i class="ti ti-moon-stars fs-2"></i>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="theme_system">
                                                            <span class="custom-option-header pb-0">
                                                                <input name="theme_mode" class="form-check-input" type="radio" value="system" id="theme_system" {{ $customization->theme_mode == 'system' ? 'checked' : '' }}>
                                                                <span class="fw-medium">Sistema</span>
                                                                <i class="ti ti-device-desktop fs-2"></i>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Color de tema -->
                                        <div class="mb-3">
                                            <label class="form-label">Color del Tema</label>
                                            <div class="row">
                                                @foreach(\App\Models\UserCustomization::getThemeColors() as $color => $hex)
                                                <div class="col-4 mb-2">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="color_{{ $color }}">
                                                            <span class="custom-option-header pb-0">
                                                                <input name="theme_color" class="form-check-input" type="radio" value="{{ $color }}" id="color_{{ $color }}" {{ $customization->theme_color == $color ? 'checked' : '' }}>
                                                                <span class="fw-medium">{{ ucfirst($color) }}</span>
                                                                <span class="btn waves-effect text-white px-2" style="background-color: {{ $hex }};">
                                                                    <i class="ti ti-palette"></i>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuración de Fuente -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="ti ti-typography me-2"></i>
                                            Configuración de Fuente
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Familia de fuente -->
                                        <div class="mb-3">
                                            <label for="font_family" class="form-label">Familia de Fuente</label>
                                            <select class="form-select" name="font_family" id="font_family">
                                                @foreach(\App\Models\UserCustomization::getFontFamilies() as $value => $name)
                                                    <option value="{{ $value }}" {{ $customization->font_family == $value ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Tamaño de fuente -->
                                        <div class="mb-3">
                                            <label class="form-label">Tamaño de Fuente</label>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="font_small">
                                                            <input name="font_size" class="form-check-input" type="radio" value="small" id="font_small" {{ $customization->font_size == 'small' ? 'checked' : '' }}>
                                                            <span class="custom-option-header pb-1">
                                                                <span class="fw-medium" style="font-size: 12px;">Pequeña</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="font_medium">
                                                            <input name="font_size" class="form-check-input" type="radio" value="medium" id="font_medium" {{ $customization->font_size == 'medium' ? 'checked' : '' }}>
                                                            <span class="custom-option-header pb-1">
                                                                <span class="fw-medium" style="font-size: 14px;">Mediana</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="font_large">
                                                            <input name="font_size" class="form-check-input" type="radio" value="large" id="font_large" {{ $customization->font_size == 'large' ? 'checked' : '' }}>
                                                            <span class="custom-option-header pb-1">
                                                                <span class="fw-medium" style="font-size: 16px;">Grande</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuración de Layout -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="ti ti-layout-dashboard me-2"></i>
                                            Configuración de Layout
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Tipo de layout -->
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Navegación</label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="layout_vertical">
                                                            <span class="custom-option-header pb-0">
                                                                <input name="layout_type" class="form-check-input" type="radio" value="vertical" id="layout_vertical" {{ $customization->layout_type == 'vertical' ? 'checked' : '' }}>
                                                                <span class="fw-medium">Vertical</span>
                                                                <i class="ti ti-layout-sidebar fs-2"></i>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="layout_horizontal">
                                                            <span class="custom-option-header pb-0">
                                                                <input name="layout_type" class="form-check-input" type="radio" value="horizontal" id="layout_horizontal" {{ $customization->layout_type == 'horizontal' ? 'checked' : '' }}>
                                                                <span class="fw-medium">Horizontal</span>
                                                                <i class="ti ti-layout-navbar fs-2"></i>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Container del layout -->
                                        <div class="mb-3">
                                            <label class="form-label">Contenedor</label>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="container_fluid">
                                                            <span class="custom-option-header">
                                                                <input name="layout_container" class="form-check-input" type="radio" value="fluid" id="container_fluid" {{ $customization->layout_container == 'fluid' ? 'checked' : '' }}>
                                                                <span class="fw-medium">Fluido</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="container_boxed">
                                                            <span class="custom-option-header">
                                                                <input name="layout_container" class="form-check-input" type="radio" value="boxed" id="container_boxed" {{ $customization->layout_container == 'boxed' ? 'checked' : '' }}>
                                                                <span class="fw-medium">Caja</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="container_detached">
                                                            <span class="custom-option-header">
                                                                <input name="layout_container" class="form-check-input" type="radio" value="detached" id="container_detached" {{ $customization->layout_container == 'detached' ? 'checked' : '' }}>
                                                                <span class="fw-medium">Separado</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuración de Navbar -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="ti ti-layout-navbar me-2"></i>
                                            Configuración de Navbar
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Tipo de navbar -->
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Navbar</label>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="navbar_fixed">
                                                            <span class="custom-option-header pb-0">
                                                                <input name="navbar_type" class="form-check-input" type="radio" value="fixed" id="navbar_fixed" {{ $customization->navbar_type == 'fixed' ? 'checked' : '' }}>
                                                                <span class="fw-medium">Fijo</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="navbar_static">
                                                            <span class="custom-option-header pb-0">
                                                                <input name="navbar_type" class="form-check-input" type="radio" value="static" id="navbar_static" {{ $customization->navbar_type == 'static' ? 'checked' : '' }}>
                                                                <span class="fw-medium">Estático</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content" for="navbar_hidden">
                                                            <span class="custom-option-header pb-0">
                                                                <input name="navbar_type" class="form-check-input" type="radio" value="hidden" id="navbar_hidden" {{ $customization->navbar_type == 'hidden' ? 'checked' : '' }}>
                                                                <span class="fw-medium">Oculto</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Opciones adicionales -->
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="navbar_blur" id="navbar_blur" {{ $customization->navbar_blur ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="navbar_blur">
                                                        Efecto Blur
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6" id="sidebar-options" style="display: {{ $customization->layout_type == 'vertical' ? 'block' : 'none' }}">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="sidebar_collapsed" id="sidebar_collapsed" {{ $customization->sidebar_collapsed ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sidebar_collapsed">
                                                        Sidebar Colapsado
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-check me-1"></i>
                                Guardar Configuración
                            </button>
                            <button type="button" class="btn btn-label-warning" onclick="resetCustomization()">
                                <i class="ti ti-refresh me-1"></i>
                                Restablecer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const form = document.getElementById('customization-form');

        // Función para mostrar alertas
        function showAlert(type, message) {
            const alertsContainer = document.getElementById('customization-alerts');
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible d-flex" role="alert">
                    <span class="alert-icon rounded">
                        <i class="ti ti-${type === 'success' ? 'check' : 'x'}"></i>
                    </span>
                    <div>
                        <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">
                            ${type === 'success' ? '¡Éxito!' : 'Error'}
                        </h6>
                        <p class="mb-0">${message}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            alertsContainer.innerHTML = alertHtml;
            
            // Auto-dismiss después de 5 segundos
            setTimeout(() => {
                const alert = alertsContainer.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }

        // Función para convertir hex a rgb (solo para uso interno)
        function hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? 
                parseInt(result[1], 16) + ', ' + parseInt(result[2], 16) + ', ' + parseInt(result[3], 16) : '105, 108, 255';
        }

        // Event listeners para mostrar/ocultar secciones
        form.addEventListener('change', function(e) {
            // Mostrar/ocultar sección de color personalizado
            if (e.target.name === 'theme_color') {
                const customColorContainer = document.getElementById('custom-color-container');
                if (customColorContainer) {
                    customColorContainer.style.display = e.target.value === 'custom' ? 'block' : 'none';
                }
            }
            
            // Mostrar/ocultar configuraciones de sidebar
            if (e.target.name === 'layout_type') {
                const sidebarOptions = document.getElementById('sidebar-options');
                if (sidebarOptions) {
                    sidebarOptions.style.display = e.target.value === 'vertical' ? 'block' : 'none';
                }
            }
        });

        // Función para guardar configuración
        function saveCustomization() {
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Manejar checkboxes para convertir a boolean correcto
            const navbarBlur = document.getElementById('navbar_blur');
            const sidebarCollapsed = document.getElementById('sidebar_collapsed');
            
            // Para checkboxes no marcados, FormData no los incluye, así que los agregamos manualmente
            formData.set('navbar_blur', navbarBlur && navbarBlur.checked ? '1' : '0');
            formData.set('sidebar_collapsed', sidebarCollapsed && sidebarCollapsed.checked ? '1' : '0');
            
            console.log('Datos del formulario:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Mostrar loading
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Guardando...';
            submitBtn.disabled = true;
            
            fetch('{{ route("customization.update") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response type:', response.type);
                
                // Obtener el texto de la respuesta para debuggear
                return response.text().then(text => {
                    console.log('Response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.log('Response was not JSON:', text.substring(0, 500));
                        throw new Error('Response was not JSON: ' + text.substring(0, 100));
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    
                    showAlert('success', data.message);
                    // Recargar página para aplicar cambios
                    setTimeout(() => { window.location.reload(); }, 1500);

                } else {
                    showAlert('danger', data.message || 'Error al guardar la configuración');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Error de conexión al guardar la configuración');
                // Solo restaurar botón si hay error
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        // Envío del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            saveCustomization();
        });

        // Inicializar visibilidad de secciones
        const themeColorChecked = document.querySelector('input[name="theme_color"]:checked');
        if (themeColorChecked) {
            const customColorContainer = document.getElementById('custom-color-container');
            if (customColorContainer) {
                customColorContainer.style.display = themeColorChecked.value === 'custom' ? 'block' : 'none';
            }
        }
        
        const layoutTypeChecked = document.querySelector('input[name="layout_type"]:checked');
        if (layoutTypeChecked) {
            const sidebarOptions = document.getElementById('sidebar-options');
            if (sidebarOptions) {
                sidebarOptions.style.display = layoutTypeChecked.value === 'vertical' ? 'block' : 'none';
            }
        }
    });

    // Función global para resetear configuración
    function resetCustomization() {
        if (confirm('¿Estás seguro de restablecer todas las configuraciones a los valores por defecto?')) {
            // Buscar todos los botones de reset en la página
            const resetButtons = document.querySelectorAll('button[onclick*="resetCustomization"]');
            
            resetButtons.forEach(btn => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Restableciendo...';
                btn.disabled = true;
                
                // Guardar texto original para restaurar después
                btn.dataset.originalText = originalText;
            });
            
            fetch('{{ route("customization.reset") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar alerta de éxito
                    const alertsContainer = document.getElementById('customization-alerts');
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible d-flex" role="alert">
                            <span class="alert-icon rounded">
                                <i class="ti ti-check"></i>
                            </span>
                            <div>
                                <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">
                                    ¡Éxito!
                                </h6>
                                <p class="mb-0">${data.message}</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    alertsContainer.innerHTML = alertHtml;
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert('Error al restablecer la configuración: ' + (data.message || 'Error desconocido'));
                    // Restaurar botones solo si hay error
                    const resetButtons = document.querySelectorAll('button[onclick*="resetCustomization"]');
                    resetButtons.forEach(btn => {
                        btn.innerHTML = btn.dataset.originalText || '<i class="ti ti-refresh me-1"></i>Restablecer';
                        btn.disabled = false;
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al restablecer la configuración');
                // Restaurar botones en caso de error
                const resetButtons = document.querySelectorAll('button[onclick*="resetCustomization"]');
                resetButtons.forEach(btn => {
                    btn.innerHTML = btn.dataset.originalText || '<i class="ti ti-refresh me-1"></i>Restablecer';
                    btn.disabled = false;
                });
            });
        }
    }
</script>
@endpush