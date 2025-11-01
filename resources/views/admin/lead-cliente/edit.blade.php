@extends('layouts.adm')

@section('title', 'Editar Lead Cliente | ERP Multisoft')

@php
    $breadcrumbs = [
        'title' => 'Editar Lead Cliente',
        'description' => 'Modificar información del lead cliente',
        'icon' => 'ti ti-edit',
        'items' => [
            ['name' => 'CRM', 'url' => 'javascript:void(0)'],
            ['name' => 'Leads Clientes', 'url' => route('admin.lead-cliente.index')],
            ['name' => 'Editar', 'url' => 'javascript:void(0)', 'active' => true],
        ],
    ];
@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-erp.breadcrumbs :items="$breadcrumbs">
            <x-slot:extra>
                @can('lead_cliente.view')
                    <a href="{{ route('admin.lead-cliente.show', $leadCliente) }}" class="btn btn-label-secondary waves-effect">
                        <i class="ti ti-eye me-2"></i>
                        Ver
                    </a>
                @endcan
                <a href="{{ route('admin.lead-cliente.index') }}" class="btn btn-label-dark waves-effect">
                    <i class="ti ti-arrow-left me-2"></i>
                    Regresar
                </a>
            </x-slot:extra>
        </x-erp.breadcrumbs>

        <form action="{{ route('admin.lead-cliente.update', $leadCliente) }}" method="POST" id="editLeadForm">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-edit me-2"></i>
                                Editar Lead Cliente
                            </h5>
                            <small class="text-muted">Actualiza la información del lead cliente</small>
                        </div>
                        <div class="card-body">
                            <!-- Información de la Empresa -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="ti ti-building me-2"></i>
                                        Información de la Empresa
                                    </h6>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="empresa" class="form-label">Nombre de la Empresa <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('empresa') is-invalid @enderror" 
                                           id="empresa" 
                                           name="empresa" 
                                           value="{{ old('empresa', $leadCliente->empresa) }}" 
                                           placeholder="Ingrese el nombre de la empresa">
                                    @error('empresa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ruc" class="form-label">RUC/NIT <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('ruc') is-invalid @enderror" 
                                           id="ruc" 
                                           name="ruc" 
                                           value="{{ old('ruc', $leadCliente->ruc) }}" 
                                           placeholder="Ingrese RUC o NIT">
                                    @error('ruc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="rubro_empresa" class="form-label">Rubro de la Empresa</label>
                                    <select class="form-select @error('rubro_empresa') is-invalid @enderror" 
                                            id="rubro_empresa" 
                                            name="rubro_empresa">
                                        <option value="">Seleccione un rubro</option>
                                        <option value="Tecnología" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Tecnología' ? 'selected' : '' }}>Tecnología</option>
                                        <option value="Comercio" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Comercio' ? 'selected' : '' }}>Comercio</option>
                                        <option value="Servicios" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Servicios' ? 'selected' : '' }}>Servicios</option>
                                        <option value="Manufactura" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Manufactura' ? 'selected' : '' }}>Manufactura</option>
                                        <option value="Construcción" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Construcción' ? 'selected' : '' }}>Construcción</option>
                                        <option value="Alimentación" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Alimentación' ? 'selected' : '' }}>Alimentación</option>
                                        <option value="Salud" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Salud' ? 'selected' : '' }}>Salud</option>
                                        <option value="Educación" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Educación' ? 'selected' : '' }}>Educación</option>
                                        <option value="Turismo" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Turismo' ? 'selected' : '' }}>Turismo</option>
                                        <option value="Agricultura" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Agricultura' ? 'selected' : '' }}>Agricultura</option>
                                        <option value="Transporte" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Transporte' ? 'selected' : '' }}>Transporte</option>
                                        <option value="Financiero" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Financiero' ? 'selected' : '' }}>Financiero</option>
                                        <option value="Inmobiliario" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Inmobiliario' ? 'selected' : '' }}>Inmobiliario</option>
                                        <option value="Consultoría" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Consultoría' ? 'selected' : '' }}>Consultoría</option>
                                        <option value="Otro" {{ old('rubro_empresa', $leadCliente->rubro_empresa) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('rubro_empresa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nro_empleados" class="form-label">Número de Empleados</label>
                                    <select class="form-select @error('nro_empleados') is-invalid @enderror" 
                                            id="nro_empleados" 
                                            name="nro_empleados">
                                        <option value="">Seleccione rango</option>
                                        <option value="1-5" {{ old('nro_empleados', $leadCliente->nro_empleados) == '1-5' ? 'selected' : '' }}>1-5 empleados</option>
                                        <option value="6-20" {{ old('nro_empleados', $leadCliente->nro_empleados) == '6-20' ? 'selected' : '' }}>6-20 empleados</option>
                                        <option value="21-50" {{ old('nro_empleados', $leadCliente->nro_empleados) == '21-50' ? 'selected' : '' }}>21-50 empleados</option>
                                        <option value="51-100" {{ old('nro_empleados', $leadCliente->nro_empleados) == '51-100' ? 'selected' : '' }}>51-100 empleados</option>
                                        <option value="101-250" {{ old('nro_empleados', $leadCliente->nro_empleados) == '101-250' ? 'selected' : '' }}>101-250 empleados</option>
                                        <option value="251-500" {{ old('nro_empleados', $leadCliente->nro_empleados) == '251-500' ? 'selected' : '' }}>251-500 empleados</option>
                                        <option value="500+" {{ old('nro_empleados', $leadCliente->nro_empleados) == '500+' ? 'selected' : '' }}>Más de 500 empleados</option>
                                    </select>
                                    @error('nro_empleados')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pais" class="form-label">País</label>
                                    <select class="form-select @error('pais') is-invalid @enderror" 
                                            id="pais" 
                                            name="pais">
                                        <option value="">Seleccione un país</option>
                                        <option value="Argentina" {{ old('pais', $leadCliente->pais) == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                                        <option value="Bolivia" {{ old('pais', $leadCliente->pais) == 'Bolivia' ? 'selected' : '' }}>Bolivia</option>
                                        <option value="Brasil" {{ old('pais', $leadCliente->pais) == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                                        <option value="Chile" {{ old('pais', $leadCliente->pais) == 'Chile' ? 'selected' : '' }}>Chile</option>
                                        <option value="Colombia" {{ old('pais', $leadCliente->pais) == 'Colombia' ? 'selected' : '' }}>Colombia</option>
                                        <option value="Costa Rica" {{ old('pais', $leadCliente->pais) == 'Costa Rica' ? 'selected' : '' }}>Costa Rica</option>
                                        <option value="Cuba" {{ old('pais', $leadCliente->pais) == 'Cuba' ? 'selected' : '' }}>Cuba</option>
                                        <option value="Ecuador" {{ old('pais', $leadCliente->pais) == 'Ecuador' ? 'selected' : '' }}>Ecuador</option>
                                        <option value="El Salvador" {{ old('pais', $leadCliente->pais) == 'El Salvador' ? 'selected' : '' }}>El Salvador</option>
                                        <option value="España" {{ old('pais', $leadCliente->pais) == 'España' ? 'selected' : '' }}>España</option>
                                        <option value="Guatemala" {{ old('pais', $leadCliente->pais) == 'Guatemala' ? 'selected' : '' }}>Guatemala</option>
                                        <option value="Honduras" {{ old('pais', $leadCliente->pais) == 'Honduras' ? 'selected' : '' }}>Honduras</option>
                                        <option value="México" {{ old('pais', $leadCliente->pais) == 'México' ? 'selected' : '' }}>México</option>
                                        <option value="Nicaragua" {{ old('pais', $leadCliente->pais) == 'Nicaragua' ? 'selected' : '' }}>Nicaragua</option>
                                        <option value="Panamá" {{ old('pais', $leadCliente->pais) == 'Panamá' ? 'selected' : '' }}>Panamá</option>
                                        <option value="Paraguay" {{ old('pais', $leadCliente->pais) == 'Paraguay' ? 'selected' : '' }}>Paraguay</option>
                                        <option value="Perú" {{ old('pais', $leadCliente->pais) == 'Perú' ? 'selected' : '' }}>Perú</option>
                                        <option value="República Dominicana" {{ old('pais', $leadCliente->pais) == 'República Dominicana' ? 'selected' : '' }}>República Dominicana</option>
                                        <option value="Uruguay" {{ old('pais', $leadCliente->pais) == 'Uruguay' ? 'selected' : '' }}>Uruguay</option>
                                        <option value="Venezuela" {{ old('pais', $leadCliente->pais) == 'Venezuela' ? 'selected' : '' }}>Venezuela</option>
                                    </select>
                                    @error('pais')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="plan_interes" class="form-label">Plan de Interés</label>
                                    <select class="form-select @error('plan_interes') is-invalid @enderror" 
                                            id="plan_interes" 
                                            name="plan_interes">
                                        <option value="">Seleccione un plan</option>
                                        <option value="demo" {{ old('plan_interes', $leadCliente->plan_interes) == 'demo' ? 'selected' : '' }}>Demo Gratuito</option>
                                        <option value="basico" {{ old('plan_interes', $leadCliente->plan_interes) == 'basico' ? 'selected' : '' }}>Plan Básico</option>
                                        <option value="profesional" {{ old('plan_interes', $leadCliente->plan_interes) == 'profesional' ? 'selected' : '' }}>Plan Profesional</option>
                                        <option value="empresarial" {{ old('plan_interes', $leadCliente->plan_interes) == 'empresarial' ? 'selected' : '' }}>Plan Empresarial</option>
                                    </select>
                                    @error('plan_interes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="descripcion" class="form-label">Descripción del Negocio</label>
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                              id="descripcion" 
                                              name="descripcion" 
                                              rows="3" 
                                              placeholder="Describa brevemente el negocio o las necesidades específicas">{{ old('descripcion', $leadCliente->descripcion) }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Información del Contacto -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="ti ti-user me-2"></i>
                                        Información del Contacto
                                    </h6>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="cliente" class="form-label">Nombre del Cliente <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('cliente') is-invalid @enderror" 
                                           id="cliente" 
                                           name="cliente" 
                                           value="{{ old('cliente', $leadCliente->cliente) }}" 
                                           placeholder="Ingrese el nombre completo">
                                    @error('cliente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nro_documento" class="form-label">Documento de Identidad</label>
                                    <input type="text" 
                                           class="form-control @error('nro_documento') is-invalid @enderror" 
                                           id="nro_documento" 
                                           name="nro_documento" 
                                           value="{{ old('nro_documento', $leadCliente->nro_documento) }}" 
                                           placeholder="Ingrese número de documento">
                                    @error('nro_documento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="correo" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('correo') is-invalid @enderror" 
                                           id="correo" 
                                           name="correo" 
                                           value="{{ old('correo', $leadCliente->correo) }}" 
                                           placeholder="ejemplo@correo.com">
                                    @error('correo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" 
                                           class="form-control @error('telefono') is-invalid @enderror" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="{{ old('telefono', $leadCliente->telefono) }}" 
                                           placeholder="+591 70123456">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="cargo" class="form-label">Cargo</label>
                                    <input type="text" 
                                           class="form-control @error('cargo') is-invalid @enderror" 
                                           id="cargo" 
                                           name="cargo" 
                                           value="{{ old('cargo', $leadCliente->cargo) }}" 
                                           placeholder="Gerente General, CEO, etc.">
                                    @error('cargo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Estado -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-settings me-2"></i>
                                Estado del Lead
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="estado" 
                                       name="estado" 
                                       value="1"
                                       {{ old('estado', $leadCliente->estado) ? 'checked' : '' }}>
                                <label class="form-check-label" for="estado">
                                    Lead Activo
                                </label>
                            </div>
                            <small class="text-muted">
                                Los leads inactivos no aparecerán en las búsquedas principales
                            </small>
                        </div>
                    </div>

                    <!-- Información del Sistema -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                Información del Sistema
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted">ID del Lead</label>
                                <p class="fw-semibold">#{{ $leadCliente->id }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de Registro</label>
                                <p class="fw-semibold">
                                    <i class="ti ti-calendar me-1"></i>
                                    {{ $leadCliente->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div>
                                <label class="form-label text-muted">Última Actualización</label>
                                <p class="fw-semibold">
                                    <i class="ti ti-clock me-1"></i>
                                    {{ $leadCliente->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Guardar Cambios
                                </button>
                                <a href="{{ route('admin.lead-cliente.show', $leadCliente) }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-eye me-2"></i>
                                    Ver Lead
                                </a>
                                <a href="{{ route('admin.lead-cliente.index') }}" class="btn btn-outline-dark">
                                    <i class="ti ti-arrow-left me-2"></i>
                                    Regresar al Listado
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validación del formulario
            const form = document.getElementById('editLeadForm');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validaciones básicas
                let isValid = true;
                const requiredFields = ['empresa', 'ruc', 'cliente', 'correo'];
                
                requiredFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value.trim()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                
                // Validación de email
                const emailInput = document.getElementById('correo');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailInput.value && !emailRegex.test(emailInput.value)) {
                    emailInput.classList.add('is-invalid');
                    isValid = false;
                }
                
                if (isValid) {
                    // Mostrar confirmación antes de enviar
                    Swal.fire({
                        title: '¿Guardar cambios?',
                        text: 'Se actualizará la información del lead cliente',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, guardar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-outline-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error en el formulario',
                        text: 'Por favor, complete todos los campos requeridos correctamente',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }
            });
            
            // Eliminar clases de error al escribir
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });
        });
    </script>
@endsection