@extends('layouts.vuexy')

@section('title', 'Test - ERP Multisoft')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🧪 Prueba de Sistema</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <i class="bx bx-check-circle me-2"></i>
                    ¡El layout Vuexy funciona correctamente!
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>📊 Estadísticas del Sistema:</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total de usuarios:</span>
                                <strong>{{ App\Models\User::count() }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total de roles:</span>
                                <strong>{{ Spatie\Permission\Models\Role::count() }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total de permisos:</span>
                                <strong>{{ Spatie\Permission\Models\Permission::count() }}</strong>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>🔗 Enlaces de Prueba:</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-primary">
                                <i class="bx bx-user me-2"></i>
                                Ir a Usuarios
                            </a>
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-info">
                                <i class="bx bx-shield me-2"></i>
                                Ir a Roles
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-success">
                                <i class="bx bx-home me-2"></i>
                                Ir al Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-12">
                        <h6>👤 Información del Usuario Actual:</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <span class="avatar-initial rounded-circle bg-primary">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                        <small class="text-muted">{{ Auth::user()->email }}</small>
                                        <br>
                                        <div class="mt-1">
                                            @foreach(Auth::user()->roles as $role)
                                                <span class="badge bg-primary me-1">{{ ucfirst($role->name) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection