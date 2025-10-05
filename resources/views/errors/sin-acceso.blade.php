@extends('layouts.vuexy')

@section('title', 'Acceso Denegado')

@section('content')
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            <h2 class="mb-2 mx-2">Acceso Denegado</h2>
            <p class="mb-4 mx-2">{{ $mensaje ?? 'No tienes permisos para acceder a este recurso.' }}</p>

            <div class="mt-4">
                <img src="{{ asset('vuexy/img/illustrations/page-misc-error-light.png') }}" alt="page-misc-error-light"
                    width="500" class="img-fluid" data-app-dark-img="illustrations/page-misc-error-dark.png"
                    data-app-light-img="illustrations/page-misc-error-light.png" />
            </div>

            <div class="mt-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="ti ti-arrow-left me-1"></i>Volver al Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="ti ti-login me-1"></i>Iniciar Sesión
                    </a>
                @endauth
            </div>
        </div>
    </div>
@endsection
