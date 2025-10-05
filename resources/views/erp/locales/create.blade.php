@extends('layouts.vuexy')

@section('title', 'Crear Local')

@php

    $dataBreadcrumb = [
        'title' => 'Gestión de Locales',
        'description' => 'Administra los locales del sistema',
        'icon' => 'ti ti-building',
        'breadcrumbs' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Locales', 'url' => route('locales.index')],
            ['name' => 'Crear local', 'url' => 'javascript:void(0);'],
        ],
        'actions' => [
            [
                'name' => 'Regresar',
                'url' => route('locales.index'),
                'typeButton' => 'btn-label-dark',
                'icon' => 'ti ti-arrow-left',
                'permission' => 'locales.view',
            ],
        ],
    ];

    $dataHeaderCard = [
        'title' => 'Formulario de registro',
        'description' => '',
        'textColor' => '',
        'icon' => 'ti ti-plus',
        'iconColor' => 'bg-label-info',
        'actions' => [],
    ];

@endphp


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        @include('layouts.vuexy.breadcrumb', $dataBreadcrumb)

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    
                    @include('layouts.vuexy.header-card', $dataHeaderCard)

                    <div class="card-body">

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="ti ti-error me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('locales.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="descripcion" class="form-label">Descripción <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('descripcion') is-invalid @enderror"
                                        id="descripcion" name="descripcion" value="{{ old('descripcion') }}" required
                                        placeholder="Ej: Local Principal Centro">
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="codigo" class="form-label">Código <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                        id="codigo" name="codigo" value="{{ old('codigo') }}" required
                                        placeholder="Ej: LOC001" style="text-transform: uppercase">
                                    @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Solo letras, números, guiones y guiones bajos</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="sede_id" class="form-label">Sede <span class="text-danger">*</span></label>
                                <select class="form-select @error('sede_id') is-invalid @enderror" id="sede_id"
                                    name="sede_id" required>
                                    <option value="">Seleccionar sede...</option>
                                    @foreach ($sedes as $sede)
                                        <option value="{{ $sede->id }}"
                                            {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                                            {{ $sede->nombre }} - {{ $sede->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sede_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" rows="3"
                                    placeholder="Dirección completa del local">{{ old('direccion') }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="correo" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control @error('correo') is-invalid @enderror"
                                        id="correo" name="correo" value="{{ old('correo') }}"
                                        placeholder="correo@empresa.com">
                                    @error('correo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                        id="telefono" name="telefono" value="{{ old('telefono') }}"
                                        placeholder="+51 987654321">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="whatsapp" class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control @error('whatsapp') is-invalid @enderror"
                                        id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}"
                                        placeholder="+51 987654321">
                                    @error('whatsapp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="estado" name="estado"
                                        value="1" {{ old('estado', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="estado">
                                        Local activo
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between gap-2">
                                <a href="{{ route('locales.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x me-1"></i>
                                    Cancelar
                                </a>
                                @can('locales.create')
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>
                                    Guardar Local
                                </button>
                                @endcan
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-help-circle me-2"></i>
                            Ayuda
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="ti ti-info-circle me-1"></i>
                                Información importante
                            </h6>
                            <ul class="mb-0 ps-3">
                                <li>El código debe ser único en todo el sistema</li>
                                <li>Los campos marcados con (*) son obligatorios</li>
                                <li>El local estará asociado a la sede seleccionada</li>
                                <li>Puedes modificar esta información posteriormente</li>
                            </ul>
                        </div>

                        <div class="mt-3">
                            <h6>Formato de código recomendado:</h6>
                            <code>LOC001, LOC002, LOC003...</code>
                            <p class="text-muted mt-2">
                                <small>Usa un formato consistente para facilitar la identificación</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        // Auto-generar código basado en descripción
        document.getElementById('descripcion').addEventListener('input', function() {
            const descripcion = this.value.toUpperCase();
            const codigoField = document.getElementById('codigo');

            if (codigoField.value === '') {
                // Generar código simple basado en las primeras letras
                const palabras = descripcion.split(' ');
                let codigo = '';

                if (palabras.length >= 2) {
                    codigo = palabras[0].substring(0, 3) + palabras[1].substring(0, 3);
                } else {
                    codigo = descripcion.substring(0, 6);
                }

                // Limpiar caracteres especiales
                codigo = codigo.replace(/[^A-Z0-9]/g, '');
                codigoField.value = codigo;
            }
        });

        // Convertir código a mayúsculas
        document.getElementById('codigo').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
@endsection
