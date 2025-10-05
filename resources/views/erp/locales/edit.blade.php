@extends('layouts.vuexy')

@section('title', 'Editar Local')

@php

    $dataBreadcrumb = [
        'title' => 'Gestión de Locales',
        'description' => '',
        'icon' => 'ti ti-building-store',
        'breadcrumbs' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Locales', 'url' => route('locales.index')],
            ['name' => 'Editar local', 'url' => 'javascript:void(0)', 'active' => true],
        ],
        'actions' => [
            [
                'name' => 'Regresar',
                'url' => route('locales.index'),
                'typeButton' => 'btn-label-dark',
                'icon' => 'ti ti-arrow-left',
                'permission' => 'locales.view'
            ],

        ],
    ];

    $dataHeaderCard = [
        'title' => 'Editando el local',
        'description' => $local->descripcion,
        'textColor' => 'text-warning',
        'icon' => 'ti ti-edit',
        'iconColor' => 'bg-label-warning',        
        'actions' => [
            [
                'typeAction' => 'btnInfo',
                'name' => $local->estado == 1 ? 'ACTIVO' : 'SUSPENDIDO',
                'url' => '#',
                'icon' => $local->estado == 1 ? 'ti ti-check' : 'ti ti-x',
                'permission' => null,
                'typeButton' => $local->estado == 1 ? 'btn-label-success' : 'btn-label-danger',
            ],
            [
                'typeAction' => 'btnLink',
                'name' => 'Ver detalle',
                'url' => route('locales.show', $local),
                'typeButton' => 'btn-info',
                'icon' => 'ti ti-list-search',
                'permission' => 'locales.view'
            ],
            
        ],
    ];

@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb Component -->
    @include('layouts.vuexy.breadcrumb', $dataBreadcrumb)

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">

                {{-- $dataHeaderCard  --}}
                @include('layouts.vuexy.header-card', $dataHeaderCard)

                
                <div class="card-body">
                    <!-- Mensajes de estado -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible d-flex" role="alert">
                            <span class="alert-icon rounded"><i class="ti ti-check"></i></span>
                            <div>
                                <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">¡Éxito!</h6>
                                <p class="mb-0">{{ session('success') }}</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bx bx-error-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <form action="{{ route('locales.update', $local) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control @error('descripcion') is-invalid @enderror" 
                                    id="descripcion" 
                                    name="descripcion" 
                                    value="{{ old('descripcion', $local->descripcion) }}" 
                                    required
                                    placeholder="Ej: Local Principal Centro"
                                >
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control @error('codigo') is-invalid @enderror" 
                                    id="codigo" 
                                    name="codigo" 
                                    value="{{ old('codigo', $local->codigo) }}" 
                                    required
                                    placeholder="Ej: LOC001"
                                    style="text-transform: uppercase"
                                >
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Solo letras, números, guiones y guiones bajos</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sede_id" class="form-label">Sede <span class="text-danger">*</span></label>
                            <select class="form-select @error('sede_id') is-invalid @enderror" id="sede_id" name="sede_id" required>
                                <option value="">Seleccionar sede...</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}" 
                                        {{ old('sede_id', $local->sede_id) == $sede->id ? 'selected' : '' }}>
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
                            <textarea 
                                class="form-control @error('direccion') is-invalid @enderror" 
                                id="direccion" 
                                name="direccion" 
                                rows="3"
                                placeholder="Dirección completa del local"
                            >{{ old('direccion', $local->direccion) }}</textarea>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input 
                                    type="email" 
                                    class="form-control @error('correo') is-invalid @enderror" 
                                    id="correo" 
                                    name="correo" 
                                    value="{{ old('correo', $local->correo) }}"
                                    placeholder="correo@empresa.com"
                                >
                                @error('correo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('telefono') is-invalid @enderror" 
                                    id="telefono" 
                                    name="telefono" 
                                    value="{{ old('telefono', $local->telefono) }}"
                                    placeholder="+51 987654321"
                                >
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="whatsapp" class="form-label">WhatsApp</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('whatsapp') is-invalid @enderror" 
                                    id="whatsapp" 
                                    name="whatsapp" 
                                    value="{{ old('whatsapp', $local->whatsapp) }}"
                                    placeholder="+51 987654321"
                                >
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="estado" 
                                    name="estado" 
                                    value="1" 
                                    {{ old('estado', $local->estado) ? 'checked' : '' }}
                                >
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
                            @can('locales.edit')
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-1"></i>
                                Actualizar Local
                            </button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>        

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-info-circle me-2"></i>
                        Información del Local
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold">ID:</td>
                            <td>{{ $local->id }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Creado:</td>
                            <td>{{ $local->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Actualizado:</td>
                            <td>{{ $local->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Estado:</td>
                            <td>
                                <span class="badge bg-{{ $local->estado ? 'success' : 'secondary' }}">
                                    {{ $local->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-help-circle me-2"></i>
                        Ayuda
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bx bx-info-circle me-1"></i>
                            Información importante
                        </h6>
                        <ul class="mb-0 ps-3">
                            <li>El código debe ser único en todo el sistema</li>
                            <li>Los campos marcados con (*) son obligatorios</li>
                            <li>Los cambios se aplicarán inmediatamente</li>
                            <li>El historial de cambios se registra automáticamente</li>
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