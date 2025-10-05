@extends('layouts.vuexy')

@section('title', 'Mi Perfil')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <h4 class="mb-4">
        <i class="ti ti-user me-2"></i>Mi Perfil
    </h4>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        {{-- Información del perfil --}}
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="{{ $user->avatar_url }}" alt="Avatar" 
                             class="rounded-circle" width="120" height="120">
                    </div>
                    
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    @if($user->rol_principal)
                    <span class="badge bg-label-primary mb-3">
                        <i class="ti ti-shield me-1"></i>{{ ucfirst($user->rol_principal) }}
                    </span>
                    @endif
                    
                    {{-- Cambiar avatar --}}
                    <form action="{{ route('perfil.avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="ti ti-upload me-1"></i>Actualizar Avatar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Formulario de perfil --}}
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información Personal</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('perfil.update') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="name" class="form-control" 
                                       value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" 
                                       value="{{ old('telefono', $user->telefono) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control" 
                                       value="{{ old('fecha_nacimiento', $user->fecha_nacimiento?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Documento</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" 
                                               value="{{ $user->documento_tipo ?? 'DNI' }}" readonly>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" 
                                               value="{{ $user->documento_numero }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-x me-1"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Información adicional --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 text-muted">Grupo Empresarial</p>
                            <p class="mb-0 fw-semibold">
                                {{ $user->grupoEmpresa->nombre ?? 'No asignado' }}
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <p class="mb-1 text-muted">Empresa Principal</p>
                            <p class="mb-0 fw-semibold">
                                {{ $user->empresa->nombre ?? 'No asignado' }}
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <p class="mb-1 text-muted">Último Acceso</p>
                            <p class="mb-0 fw-semibold">
                                {{ $user->ultimo_acceso?->format('d/m/Y H:i') ?? 'Nunca' }}
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <p class="mb-1 text-muted">Registrado desde</p>
                            <p class="mb-0 fw-semibold">
                                {{ $user->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
