@extends('layouts.app')

@section('title', 'Sin Empresas Asignadas')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center">
                <!-- Icono -->
                <div class="mb-4">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 120px; height: 120px;">
                        <i class="fas fa-building-slash fa-4x text-warning"></i>
                    </div>
                </div>

                <!-- Título -->
                <h1 class="display-5 fw-bold mb-3">
                    No tienes empresas asignadas
                </h1>

                <!-- Descripción -->
                <p class="lead text-muted mb-4">
                    Actualmente no tienes acceso a ninguna empresa en el sistema.
                    Para poder comenzar a trabajar, necesitas que un administrador te asigne al menos una empresa.
                </p>

                <!-- Información del usuario -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-user-circle me-2 text-primary"></i>
                            Tu información
                        </h5>
                        <div class="row text-start">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Nombre:</strong><br>
                                    {{ $user->name }}
                                </p>
                                <p class="mb-2">
                                    <strong>Email:</strong><br>
                                    {{ $user->email }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                @if($user->grupoEmpresa)
                                    <p class="mb-2">
                                        <strong>Grupo Empresarial:</strong><br>
                                        {{ $user->grupoEmpresa->nombre }}
                                    </p>
                                @else
                                    <p class="mb-2 text-muted">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Sin grupo empresarial asignado
                                    </p>
                                @endif
                                
                                @if($user->rol_principal)
                                    <p class="mb-2">
                                        <strong>Rol:</strong><br>
                                        <span class="badge bg-secondary">{{ $user->rol_principal }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instrucciones -->
                <div class="alert alert-info text-start">
                    <h5 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>
                        ¿Qué puedes hacer?
                    </h5>
                    <hr>
                    <ul class="mb-0">
                        <li class="mb-2">
                            <strong>Contacta con tu administrador:</strong> 
                            Solicita que te asignen a una o más empresas del sistema.
                        </li>
                        <li class="mb-2">
                            <strong>Verifica tu grupo empresarial:</strong> 
                            Asegúrate de que estás asignado al grupo correcto.
                        </li>
                        <li>
                            <strong>Revisa tus permisos:</strong> 
                            Es posible que tu cuenta esté pendiente de activación.
                        </li>
                    </ul>
                </div>

                <!-- Información de contacto del administrador -->
                @if($user->grupoEmpresa && $user->grupoEmpresa->email)
                    <div class="card shadow-sm mb-4 border-primary">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                Contacta con tu administrador
                            </h5>
                            <p class="text-muted mb-3">
                                Para solicitar acceso a empresas, contacta con:
                            </p>
                            <a href="mailto:{{ $user->grupoEmpresa->email }}" class="btn btn-primary">
                                <i class="fas fa-envelope me-2"></i>
                                {{ $user->grupoEmpresa->email }}
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Botones de acción -->
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver al Dashboard
                    </a>

                    <button type="button" class="btn btn-outline-primary" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-2"></i>
                        Actualizar
                    </button>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>

                <!-- Nota adicional -->
                <div class="mt-5">
                    <p class="text-muted small">
                        <i class="fas fa-clock me-2"></i>
                        Si acabas de ser asignado a una empresa, puede tomar unos minutos para que los cambios se reflejen.
                        Intenta actualizar la página o cerrar sesión y volver a iniciar.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .alert {
        border-radius: 0.5rem;
    }
    
    .card {
        border-radius: 0.5rem;
    }
</style>
@endpush
@endsection
