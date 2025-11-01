@extends('layouts.adm')

@section('title', 'Admin. Empresarial')

@php
    $breadcrumbs = [
        'title' => 'Admin. Empresarial',
        'description' => 'Formulario para crear un nuevo grupo empresarial',
        'icon' => 'ti ti-building-bank',
        'items' => [
            ['name' => 'Configuración del Sistema', 'url' => 'javascript:void(0)'],
            ['name' => 'Grupos Empresariales', 'url' => route('admin.grupo-empresarial.index')],
            ['name' => 'Crear', 'url' => 'javascript:void(0)', 'active' => true],
        ],
    ];
@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-erp.breadcrumbs :items="$breadcrumbs">
            <x-slot:extra>
                @can('grupo_empresarial.view')
                    <a href="{{ route('admin.grupo-empresarial.index') }}" class="btn btn-label-dark waves-effect">
                        <i class="ti ti-arrow-left me-2"></i>
                        Regresar
                    </a>
                @endcan
            </x-slot:extra>
        </x-erp.breadcrumbs>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-plus me-2"></i>
                            Información del Grupo Empresarial
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="ti ti-x me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.grupo-empresarial.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                        id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="codigo" class="form-label">Código <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                        id="codigo" name="codigo" value="{{ old('codigo') }}" required
                                        style="text-transform: uppercase" maxlength="20">
                                    @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Código único del grupo (ej: GE001)</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion"
                                    rows="3">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pais_origen" class="form-label">País de Origen</label>
                                    <input type="text" class="form-control @error('pais_origen') is-invalid @enderror"
                                        id="pais_origen" name="pais_origen" value="{{ old('pais_origen') }}">
                                    @error('pais_origen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                        id="telefono" name="telefono" value="{{ old('telefono') }}">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="sitio_web" class="form-label">Sitio Web</label>
                                    <input type="url" class="form-control @error('sitio_web') is-invalid @enderror"
                                        id="sitio_web" name="sitio_web" value="{{ old('sitio_web') }}"
                                        placeholder="https://www.ejemplo.com">
                                    @error('sitio_web')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="direccion_matriz" class="form-label">Dirección Matriz</label>
                                <textarea class="form-control @error('direccion_matriz') is-invalid @enderror" id="direccion_matriz"
                                    name="direccion_matriz" rows="3">{{ old('direccion_matriz') }}</textarea>
                                @error('direccion_matriz')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="estado" name="estado"
                                        value="1" {{ old('estado', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="estado">
                                        Grupo activo
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between gap-2">
                                <a href="{{ route('admin.grupo-empresarial.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x me-1"></i>
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>
                                    Crear Grupo Empresarial
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            Información
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="ti ti-lightbulb me-1"></i>
                                Consejos
                            </h6>
                            <ul class="mb-0 ps-3">
                                <li>El código debe ser único en el sistema</li>
                                <li>Utilice códigos descriptivos (ej: GE001, CORP01)</li>
                                <li>Los campos marcados con (*) son obligatorios</li>
                                <li>Puede agregar empresas después de crear el grupo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        // Convertir código a mayúsculas
        document.getElementById('codigo').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
@endsection

