<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                
                <div class="navbar-nav ms-auto">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/home') }}" class="nav-link">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="nav-link">Iniciar Sesión</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="nav-link">Registrarse</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="jumbotron bg-primary text-white text-center p-5 rounded mb-4">
                            <h1 class="display-4">¡Bienvenido a {{ config('app.name', 'Laravel') }}!</h1>
                            <p class="lead">Sistema ERP Multisoft - Tu solución integral de gestión empresarial</p>
                            @guest
                                <hr class="my-4" style="border-color: rgba(255,255,255,0.3);">
                                <p>Comienza a gestionar tu empresa de manera eficiente.</p>
                                <div class="d-flex gap-3 justify-content-center">
                                    <a class="btn btn-light btn-lg" href="{{ route('login') }}" role="button">Iniciar Sesión</a>
                                    @if (Route::has('register'))
                                        <a class="btn btn-outline-light btn-lg" href="{{ route('register') }}" role="button">Registrarse</a>
                                    @endif
                                </div>
                            @endguest
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-chart-line fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="card-title">Gestión Avanzada</h5>
                                        <p class="card-text">Controla todos los aspectos de tu negocio desde una sola plataforma integral.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-users fa-3x text-success"></i>
                                        </div>
                                        <h5 class="card-title">Colaboración</h5>
                                        <p class="card-text">Trabaja en equipo de manera eficiente con herramientas colaborativas.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-shield-alt fa-3x text-warning"></i>
                                        </div>
                                        <h5 class="card-title">Seguridad</h5>
                                        <p class="card-text">Tus datos están protegidos con los más altos estándares de seguridad.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @guest
                            <div class="text-center mt-4">
                                <h3>¿Listo para comenzar?</h3>
                                <p class="text-muted">Únete a miles de empresas que ya confían en nosotros.</p>
                                <a href="{{ route('register') }}" class="btn btn-success btn-lg">Crear Cuenta Gratis</a>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>