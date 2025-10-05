<!DOCTYPE html>

@php
    // Función auxiliar para obtener personalización del usuario
    function getCustomizationSafe() {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user && method_exists($user, 'getCustomization')) {
                    return $user->getCustomization();
                }
            }
        } catch (Exception $e) {
            \Log::error('Error getting user customization: ' . $e->getMessage());
        }
        
        // Retornar valores por defecto
        return (object) \App\Models\UserCustomization::getDefaults();
    }
    
    // Función auxiliar para obtener clases de tema
    function getThemeClassSafe($customization) {
        if ($customization->theme_mode === 'dark') {
            return 'dark-style';
        } elseif ($customization->theme_mode === 'light') {
            return 'light-style';
        } else { // system - por defecto usar light
            return 'light-style';
        }
    }
    
    // Función auxiliar para convertir hex a rgb
    function hexToRgbSafe($hex) {
        $hex = ltrim($hex, '#');
        if (strlen($hex) == 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            return "$r, $g, $b";
        }
        return '105, 108, 255'; // default
    }
    
    $customization = getCustomizationSafe();

    // function para optener thema de del sistema windows, linux, mac
    // $systemTheme = (new \Jenssegers\Agent\Agent())->isDarkMode() ? 'dark' : 'light';
    // if ($customization->theme_mode === 'system') {
    //     $customization->theme_mode = $systemTheme;
    // }   
    
@endphp

<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="{{ getThemeClassSafe($customization) }} layout-navbar-fixed layout-menu-fixed"
  dir="{{ $customization->rtl_mode ? 'rtl' : 'ltr' }}"
  data-theme="{{ $customization->theme_mode === 'dark' ? 'theme-dark' : ($customization->theme_mode === 'light' ? 'theme-default' : 'theme-default') }}"
  data-assets-path="{{  asset('vuexy').'/' }}"
  data-template="{{ $customization->layout_type }}-menu-template"
  data-style="{{ $customization->theme_mode }}"
>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('vuexy/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    
    @php
        $fontFamily = $customization->font_family ?? 'inter';
        $googleFonts = [
            'inter' => 'Inter:wght@300;400;500;600;700',
            'roboto' => 'Roboto:wght@300;400;500;700',
            'poppins' => 'Poppins:wght@300;400;500;600;700',
            'open_sans' => 'Open+Sans:wght@300;400;500;600;700',
            'lato' => 'Lato:wght@300;400;700'
        ];
    @endphp
    
    @if(isset($googleFonts[$fontFamily]))
        <link href="https://fonts.googleapis.com/css2?family={{ $googleFonts[$fontFamily] }}&display=swap" rel="stylesheet" />
    @endif

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/fonts/tabler-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('vuexy/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    @stack('styles')
    
    <!-- Custom User Styles -->
    <style>
        :root {
            @if($customization->theme_color === 'custom' && $customization->custom_color)
                --bs-primary: {{ $customization->custom_color }};
                --bs-primary-rgb: {{ hexToRgbSafe($customization->custom_color) }};
            @elseif($customization->theme_color !== 'default')
                @php
                    $themeColors = \App\Models\UserCustomization::getThemeColors();
                    $selectedColor = $themeColors[$customization->theme_color] ?? '#696cff';
                @endphp
                --bs-primary: {{ $selectedColor }};
                --bs-primary-rgb: {{ hexToRgbSafe($selectedColor) }};
            @endif
        }
        
        @php
            $fontFamilies = [
                'inter' => 'Inter, sans-serif',
                'roboto' => 'Roboto, sans-serif',
                'poppins' => 'Poppins, sans-serif',
                'open_sans' => 'Open Sans, sans-serif',
                'lato' => 'Lato, sans-serif'
            ];
            $selectedFont = $fontFamilies[$customization->font_family ?? 'inter'] ?? 'Inter, sans-serif';
        @endphp
        
        body {
            font-family: {{ $selectedFont }} !important;
        }
        
        @if($customization->font_size === 'small')
            html { font-size: 0.85rem; }
        @elseif($customization->font_size === 'large')
            html { font-size: 1rem; }
        @endif
        
        @if($customization->navbar_blur)
            .layout-navbar {
                backdrop-filter: blur(10px);
                background-color: rgba(255, 255, 255, 0.85) !important;
            }
            
            [data-theme="theme-dark"] .layout-navbar {
                background-color: rgba(33, 33, 33, 0.85) !important;
            }
        @endif
    </style>

    <!-- Helpers -->
    <script src="{{ asset('vuexy/vendor/js/helpers.js') }}"></script>

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <!-- <script src="{{ asset('vuexy/vendor/js/template-customizer.js') }}"></script>.  -->

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file to customize your theme -->
    <script src="{{ asset('vuexy/js/config.js') }}"></script>

    @livewireStyles
    

</head>

<body>


    @if($customization->layout_type == 'vertical')

    <!-- Layout wrapper | vertical -->

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu principal -->
            @include('layouts.vuexy.sidebar')
            <!-- / Menu principal -->

            <div class="layout-page">

                <!-- Navbar -->
                @include('layouts.vuexy.navbar')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Content -->
                        @yield('content')
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('layouts.vuexy.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->

            </div>
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

    </div>

    @else

    <!-- Layout wrapper | horizontal -->

    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">

            <!-- Menu principal -->
            @include('layouts.vuexy.headerbar')
            <!-- / Menu principal -->

            <div class="layout-page">

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Menu -->
                    @include('layouts.vuexy.menu')
                    <!-- / Menu -->

                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('layouts.vuexy.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->

            </div>

        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

    </div>


    @endif


    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('vuexy/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('vuexy/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/hammer/hammer.js') }}"></script>
    {{-- <script src="{{ asset('vuexy/vendor/libs/i18n/i18n.js') }}"></script> --}}
    <script src="{{ asset('vuexy/vendor/libs/typeahead-js/typeahead.js') }}"></script>

    <script src="{{ asset('vuexy/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    @stack('vendor-scripts')

    <!-- Main JS -->
    <script src="{{ asset('vuexy/js/main.js') }}"></script>

    <!-- Page JS -->
    @stack('scripts')

    @livewireScripts

    <!-- Error Counter for SuperAdmin -->
    @auth
        @if(auth()->user()->isSuperAdmin())
            <script>
                // Función para actualizar contador de errores en el sidebar
                function updateErrorCounter() {
                    fetch('{{ route('admin.logs.stats') }}')
                        .then(response => response.json())
                        .then(data => {
                            const errorCount = document.getElementById('errorCount');
                            if (errorCount && data.error_count_today > 0) {
                                errorCount.textContent = data.error_count_today;
                                errorCount.style.display = 'inline';
                            } else if (errorCount) {
                                errorCount.style.display = 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching log stats:', error);
                        });
                }

                // Actualizar cada 30 segundos
                $(document).ready(function() {
                    updateErrorCounter(); // Ejecutar inmediatamente
                    setInterval(updateErrorCounter, 30000); // Cada 30 segundos
                });
            </script>
        @endif
    @endauth
</body>

</html>