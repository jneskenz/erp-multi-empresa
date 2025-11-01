@extends('layouts.adm')

@section('title', 'CRM - Leads Clientes | ERP Multisoft')

{{-- @push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush --}}

@php
    $breadcrumbs = [
        'title' => 'Crear Lead Cliente',
        'description' => 'Formulario para crear un nuevo lead cliente',
        'icon' => 'ti ti-building-bank',
        'items' => [
            ['name' => 'CRM', 'url' => 'javascript:void(0)'],
            ['name' => 'Leads Clientes', 'url' => route('admin.lead-cliente.index')],
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
            <!-- Validation Wizard -->
            <div class="col-12 mb-6">
                <div id="wizard-validation" class="bs-stepper mt-2">

                    <div class="bs-stepper-header">
                        <div class="step" data-target="#account-details-validation">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">1</span>
                                <span class="bs-stepper-label mt-1">
                                    <span class="bs-stepper-title">Datos de la Empresa</span>
                                    <span class="bs-stepper-subtitle">Información empresarial</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="ti ti-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#personal-info-validation">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">2</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Datos Personales</span>
                                    <span class="bs-stepper-subtitle">Información del representante</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="ti ti-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#social-links-validation">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">3</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Plan de Suscripción</span>
                                    <span class="bs-stepper-subtitle text-success">Prueba una demo gratuita 👑</span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="bs-stepper-content">
                        <form id="wizard-validation-form" action="{{ route('admin.lead-cliente.store') }}" method="POST">
                            @csrf
                            <!-- Account Details -->
                            <div id="account-details-validation" class="content">
                                <div class="row g-6">
                                    <div class="col-sm-12">
                                        <h6 class="mb-0">Información de la Empresa</h6>
                                        <small>Ingrese los datos principales de su grupo empresarial.</small>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="nombre">
                                            Nombre de la empresa <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="nombre" id="nombre" class="form-control"
                                            placeholder="johndoe" />
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="codigo">
                                            Código RUC/NIT <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="codigo" id="codigo" class="form-control"
                                            placeholder="john.doe@email.com" aria-label="john.doe" />
                                    </div>
                                    
                                    <div class="col-sm-3 col-xs-12">
                                        <label class="form-label" for="rubro_empresa">Rubro de la empresa</label>
                                        <select class="form-select" id="rubro_empresa" name="rubro_empresa">
                                            <option value="">Seleccionar rubro</option>                                            
                                            <option value="Finanzas">Finanzas y Seguros</option>
                                            <option value="Salud">Salud</option>
                                            <option value="Manufactura">Manufactura</option>
                                            <option value="Retail">Retail</option>
                                            <option value="Servicios">Servicios</option>
                                            <option value="Tecnología">Tecnología</option>
                                            <option value="Construcción">Construcción</option>
                                            <option value="Alimentos">Alimentos</option>
                                            <option value="Logística">Logística</option>
                                            <option value="ONG_Asociaciones">ONG / Asociaciones</option>
                                            <option value="Otros">Otros</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 col-xs-12">
                                        <label for="nro_empleados" class="form-label">Número de Empleados</label>
                                        <select class="form-select @error('nro_empleados') is-invalid @enderror" 
                                                id="nro_empleados" 
                                                name="nro_empleados">
                                            <option value="">Seleccione rango</option>
                                            <option value="1-5">1-5 empleados</option>
                                            <option value="6-20">6-20 empleados</option>
                                            <option value="21-50">21-50 empleados</option>
                                            <option value="51-100">51-100 empleados</option>
                                            <option value="101-250">101-250 empleados</option>
                                            <option value="251-500">251-500 empleados</option>
                                            <option value="500+">Más de 500 empleados</option>
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
                                            <option value="Argentina" >Argentina</option>
                                            <option value="Bolivia">Bolivia</option>
                                            <option value="Brasil">Brasil</option>
                                            <option value="Chile">Chile</option>
                                            <option value="Colombia">Colombia</option>
                                            <option value="Costa Rica">Costa Rica</option>
                                            <option value="Cuba">Cuba</option>
                                            <option value="Ecuador">Ecuador</option>
                                            <option value="El Salvador">El Salvador</option>
                                            <option value="España">España</option>
                                            <option value="Guatemala">Guatemala</option>
                                            <option value="Honduras">Honduras</option>
                                            <option value="México">México</option>
                                            <option value="Nicaragua">Nicaragua</option>
                                            <option value="Panamá">Panamá</option>
                                            <option value="Paraguay">Paraguay</option>
                                            <option value="Perú">Perú</option>
                                            <option value="República Dominicana">República Dominicana</option>
                                            <option value="Uruguay">Uruguay</option>
                                            <option value="Venezuela">Venezuela</option>
                                        </select>
                                        @error('pais')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label" for="wizard_descripcion">Descripción del Negocio</label>
                                        <textarea class="form-control" id="wizard_descripcion" name="wizard_descripcion" rows="3"
                                            placeholder="Breve descripción de la actividad empresarial..."></textarea>
                                    </div>


                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev" disabled>
                                            <i class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Anterior</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-2">Siguiente</span>
                                            <i class="ti ti-arrow-right ti-xs"></i>
                                        </button>
                                    </div>
                                    <!-- Card Demo Elegante -->
                                    <div class="col-12 mt-4">
                                        <div class="card border-0 bg-gradient-primary demo-highlight-card">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-2 text-center">
                                                        <div class="avatar avatar-xl">
                                                            <span
                                                                class="avatar-initial rounded-circle bg-light text-dark">
                                                                <i class="ti ti-gift ti-lg"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h5 class="card-title text-white mb-1">
                                                            <span class="badge bg-light text-dark me-2">
                                                                <i class="ti ti-clock me-1"></i>
                                                                15 DÍAS GRATIS
                                                            </span><br>
                                                            ¡Prueba nuestra plataforma sin compromiso!
                                                        </h5>
                                                        <p class="card-text text-white mb-2 opacity-75">
                                                            Accede a todas las funcionalidades premium, configura tu empresa
                                                            y experimenta el poder de nuestro ERP sin restricciones.
                                                        </p>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <span class="badge bg-light text-dark">
                                                                <i class="ti ti-check me-1"></i>Sin tarjeta de crédito
                                                            </span>
                                                            <span class="badge bg-light text-dark">
                                                                <i class="ti ti-check me-1"></i>Configuración completa
                                                            </span>
                                                            <span class="badge bg-light text-dark">
                                                                <i class="ti ti-check me-1"></i>Soporte incluido
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <div class="demo-timer">
                                                            <div class="text-white">
                                                                <div class="fw-bold h4 mb-0">15</div>
                                                                <small class="text-center">Días gratis</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Personal Info -->
                            <div id="personal-info-validation" class="content">
                                <div class="row g-6">
                                    <div class="col-sm-12">
                                        <h6 class="mb-0">Datos Personales del Representante Legal</h6>
                                        <small>
                                            Esta información será utilizada como contacto principal y para la
                                            configuración inicial de su cuenta en el sistema.
                                        </small>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="wizard_representante_nombre">
                                            <i class="ti ti-user me-1"></i>
                                            Nombres y Apellidos
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="wizard_representante_nombre"
                                            name="wizard_representante_nombre" class="form-control"
                                            placeholder="Juan Carlos" />
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="wizard_representante_dni">
                                            <i class="ti ti-id me-1"></i>
                                            Documento de Identidad
                                        </label>
                                        <input type="text" id="wizard_representante_dni"
                                            name="wizard_representante_dni" class="form-control"
                                            placeholder="12345678" />
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="wizard_representante_telefono">
                                            <i class="ti ti-phone me-1"></i>
                                            Teléfono
                                        </label>
                                        <input type="text" id="wizard_representante_telefono"
                                            name="wizard_representante_telefono" class="form-control"
                                            placeholder="+51 999 888 777" />
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="wizard_representante_email">
                                            <i class="ti ti-mail me-1"></i>
                                            Correo
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" id="wizard_representante_email"
                                            name="wizard_representante_email" class="form-control"
                                            placeholder="juan.garcia@email.com" />
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="cargoempresa">
                                            <i class="ti ti-briefcase me-1"></i>
                                            Cargo en la Empresa
                                        </label>
                                        <select class="form-select" id="cargoempresa" name="cargoempresa">
                                            <option value="">Seleccionar cargo</option>
                                            <option value="CEO">CEO / Director Ejecutivo</option>
                                            <option value="Gerente General">Gerente General</option>
                                            <option value="Propietario">Propietario</option>
                                            <option value="Socio Fundador">Socio Fundador</option>
                                            <option value="Representante Legal">Representante Legal</option>
                                            <option value="Administrador">Administrador</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev">
                                            <i class="ti ti-arrow-left ti-xs me-sm-2 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Anterior</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-2">Siguiente</span>
                                            <i class="ti ti-arrow-right ti-xs"></i>
                                        </button>
                                    </div>

                                    <!-- Card Demo Elegante Paso 2 -->
                                    <div class="col-12">
                                        <div class="card border-0 demo-personal-card">
                                            <div class="card-body p-4">
                                                <div class="row align-items-center">
                                                    <div class="col-md-2 text-center">
                                                        <div class="demo-icon-container">
                                                            <div class="avatar avatar-lg">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-gradient-success">
                                                                    <i class="ti ti-user-star ti-lg text-white"></i>
                                                                </span>
                                                            </div>
                                                            <div class="demo-pulse"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h5 class="mb-2">
                                                            <span class="badge bg-success me-2">
                                                                <i class="ti ti-crown me-1"></i>ACCESO VIP
                                                            </span><br>
                                                            Tu cuenta tendrá privilegios completos
                                                        </h5>
                                                        <p class="text-muted mb-2">
                                                            Como parte de la prueba demo, tendrás acceso completo a todas
                                                            las funcionalidades
                                                            administrativas y de gestión empresarial durante 15 días.
                                                        </p>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="ti ti-shield-check text-success me-2"></i>
                                                                    <small class="text-muted">Datos seguros</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="ti ti-settings text-primary me-2"></i>
                                                                    <small class="text-muted">Configuración
                                                                        completa</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <div class="demo-status">
                                                            <div class="text-success">
                                                                <i class="ti ti-circle-check ti-lg mb-1"></i>
                                                                <div class="small fw-bold">Activación</div>
                                                                <div class="small text-muted">Inmediata</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- Social Links -->
                            <div id="social-links-validation" class="content">
                                <div class="content-header mb-4">
                                    <h6 class="mb-0">Planes de suscripción</h6>
                                    <small>Prueba una demo gratuita de 15 días.</small>
                                </div>
                                <div class="row g-6">


                                    <div class="col-12">
                                        <div class="row">
                                            <!-- Plan Básico -->
                                            <div class="col-md-4 mb-3">
                                                <div class="card plan-card h-100">
                                                    <div class="card-body text-center">
                                                        <div class="avatar avatar-lg mx-auto mb-3">
                                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                                <i class="ti ti-rocket"></i>
                                                            </span>
                                                        </div>
                                                        <h5 class="card-title">Básico</h5>
                                                        <div class="pricing mb-3">
                                                            <span class="h3 text-primary">S/99.90</span>
                                                            <small class="h5 text-muted">/mes</small>
                                                        </div>
                                                        <ul class="list-unstyled text-start mb-4">
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Una empresa
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Hasta 3 sedes
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Usuarios limitados
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Módulos ERP esenciales
                                                            </li>
                                                            {{-- <li class="mb-2">
                                                                    <i class="ti ti-check text-success me-2"></i>
                                                                    Soporte por email
                                                                </li> --}}
                                                            <li class="mb-2">
                                                                <i class="ti ti-x text-muted me-2"></i>
                                                                <span class="text-muted">CRM</span>
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-x text-muted me-2"></i>
                                                                <span class="text-muted">Finanzas</span>
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-x text-muted me-2"></i>
                                                                <span class="text-muted">Recursos Humanos</span>
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-x text-muted me-2"></i>
                                                                <span class="text-muted">Dominio personalizado</span>
                                                            </li>
                                                        </ul>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="plan_suscripcion" id="plan_basico" value="basico">
                                                            {{-- <label class="form-check-label" for="plan_basico">
                                                                    <span class="btn btn-outline-primary btn-sm">Seleccionar</span>
                                                                </label> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Plan Profesional -->
                                            <div class="col-md-4 mb-3">
                                                <div class="card plan-card h-100 border-primary">
                                                    <div class="card-body text-center position-relative">
                                                        <div
                                                            class="badge bg-primary position-absolute top-0 start-50 translate-middle">
                                                            Recomendado
                                                        </div>
                                                        <div class="avatar avatar-lg mx-auto mb-3 mt-3">
                                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                                <i class="ti ti-crown"></i>
                                                            </span>
                                                        </div>
                                                        <h5 class="card-title">Profesional</h5>
                                                        <div class="pricing mb-3">
                                                            <span class="h3 text-primary">S/ 239.90</span>
                                                            <small class="h5 text-muted">/mes</small>
                                                        </div>
                                                        <ul class="list-unstyled text-start mb-4">
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Hasta 5 empresas y Multi-sede
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Usuarios ilimitados
                                                            </li>

                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Reportes avanzados
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                CRM
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Finanzas
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Recursos Humanos
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Soporte
                                                            </li>
                                                        </ul>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="plan_suscripcion" id="plan_profesional"
                                                                value="profesional">
                                                            {{-- <label class="form-check-label" for="plan_profesional">
                                                                    <span class="btn btn-primary btn-sm">Seleccionar</span>
                                                                </label> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Plan Empresarial -->
                                            <div class="col-md-4 mb-3">
                                                <div class="card plan-card h-100">
                                                    <div class="card-body text-center">
                                                        <div class="avatar avatar-lg mx-auto mb-3">
                                                            <span class="avatar-initial rounded-circle bg-label-warning">
                                                                <i class="ti ti-building-skyscraper"></i>
                                                            </span>
                                                        </div>
                                                        <h5 class="card-title">Empresarial</h5>
                                                        <div class="pricing mb-3">
                                                            <span class="h3 text-primary">ERP</span>
                                                            <small class="h5 text-success">Personalizado</small>
                                                        </div>
                                                        <ul class="list-unstyled text-start mb-4">
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Personalización completa
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Empresas ilimitadas y Multi-sede
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Usuarios ilimitados
                                                            </li>

                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Reportes avanzados
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                CRM
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Finanzas
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Recursos Humanos
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="ti ti-check text-success me-2"></i>
                                                                Soporte dedicado
                                                            </li>
                                                        </ul>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="plan_suscripcion" id="plan_empresarial"
                                                                value="empresarial">
                                                            {{-- <label class="form-check-label" for="plan_empresarial">
                                                                    <span class="btn btn-outline-warning btn-sm">Seleccionar</span>
                                                                </label> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Demo Gratuito -->
                                    <div class="col-12 mb-3">
                                        <div class="card border-warning demo-card">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-2 text-center">
                                                        <div class="avatar avatar-lg">
                                                            <span class="avatar-initial rounded-circle bg-label-warning">
                                                                <i class="ti ti-gift ti-lg"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <h5 class="card-title mb-1">
                                                            <span class="badge bg-warning me-2">¡GRATIS!</span>
                                                            Prueba Demo por 15 días
                                                        </h5>
                                                        <p class="card-text mb-2">
                                                            Accede a todas las funcionalidades premium sin costo por 15
                                                            días.
                                                            No se requiere tarjeta de crédito.
                                                        </p>
                                                        <ul class="list-inline mb-0">
                                                            <li class="list-inline-item">
                                                                <i class="ti ti-check text-success me-1"></i>
                                                                <small>Acceso completo</small>
                                                            </li>
                                                            <li class="list-inline-item">
                                                                <i class="ti ti-check text-success me-1"></i>
                                                                <small>Sin compromiso</small>
                                                            </li>
                                                            <li class="list-inline-item">
                                                                <i class="ti ti-check text-success me-1"></i>
                                                                <small>Soporte incluido</small>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-3 text-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="plan_suscripcion" id="plan_demo" value="demo"
                                                                checked>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-info-circle"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h6 class="alert-heading mb-1">Información de Planes</h6>
                                                    <ul class="mb-0 ps-3">
                                                        <li><strong>Demo:</strong> Acceso completo por 15 días sin
                                                            compromiso</li>
                                                        <li><strong>Cambios:</strong> Podrá cambiar de plan en cualquier
                                                            momento</li>
                                                        <li><strong>Soporte:</strong> Incluido en todos los planes</li>
                                                        <li><strong>Facturación:</strong> Los precios no incluyen impuestos
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev">
                                            <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Anterior</span>
                                        </button>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-warning" id="btn-demo">
                                                <i class="ti ti-clock me-sm-1 me-0"></i>
                                                <span class="align-middle d-sm-inline-block d-none">Probar Demo 15
                                                    días</span>
                                            </button>
                                            <button class="btn btn-success btn-next btn-submit">
                                                <i class="ti ti-check me-sm-1 me-0"></i>
                                                <span class="align-middle d-sm-inline-block d-none">Crear Cuenta</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Validation Wizard -->
        </div>
    </div>
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Convertir código a mayúsculas
        document.getElementById('codigo').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Manejo del formulario wizard con AJAX
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('wizard-validation-form');
            const btnSubmit = document.querySelector('.btn-submit');
            const btnDemo = document.getElementById('btn-demo');
            
            // Manejar envío del formulario
            function submitForm(isDemoMode = false) {
                const formData = new FormData(form);
                
                // Si es modo demo, forzar el plan demo
                if (isDemoMode) {
                    formData.set('plan_suscripcion', 'demo');
                }
                
                // Deshabilitar botones
                btnSubmit.disabled = true;
                btnDemo.disabled = true;
                
                // Mostrar loading
                const originalSubmitText = btnSubmit.innerHTML;
                const originalDemoText = btnDemo.innerHTML;
                
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
                btnDemo.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Activando Demo...';

                // Enviar datos con fetch
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Éxito
                        Swal.fire({
                            icon: 'success',
                            title: '¡Registro Exitoso!',
                            html: `
                                <div class="text-start">
                                    <p class="mb-2"><strong>Empresa:</strong> ${data.data.empresa}</p>
                                    <p class="mb-2"><strong>Plan:</strong> ${data.data.plan.charAt(0).toUpperCase() + data.data.plan.slice(1)}</p>
                                    ${data.data.is_demo ? '<p class="text-success"><i class="ti ti-check-circle me-1"></i><strong>¡Tu demo de 15 días está activo!</strong></p>' : ''}
                                </div>
                            `,
                            confirmButtonText: 'Continuar',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirigir al listado
                                window.location.href = '{{ route("admin.lead-cliente.index") }}';
                            }
                        });
                    } else {
                        // Error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en el Registro',
                            text: data.message || 'Ocurrió un error inesperado',
                            confirmButtonText: 'Intentar Nuevamente',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: 'No se pudo conectar con el servidor. Por favor, verifica tu conexión e inténtalo nuevamente.',
                        confirmButtonText: 'Entendido',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        }
                    });
                })
                .finally(() => {
                    // Restaurar botones
                    btnSubmit.disabled = false;
                    btnDemo.disabled = false;
                    btnSubmit.innerHTML = originalSubmitText;
                    btnDemo.innerHTML = originalDemoText;
                });
            }

            // Validación básica del formulario
            function validateForm() {
                const requiredFields = [
                    { id: 'nombre', name: 'Nombre de la empresa' },
                    { id: 'codigo', name: 'Código RUC/NIT' },
                    { id: 'wizard_representante_nombre', name: 'Nombre del representante' },
                    { id: 'wizard_representante_email', name: 'Email del representante' }
                ];

                for (let field of requiredFields) {
                    const element = document.getElementById(field.id);
                    if (!element || !element.value.trim()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Campo Requerido',
                            text: `El campo "${field.name}" es obligatorio.`,
                            confirmButtonText: 'Entendido',
                            customClass: {
                                confirmButton: 'btn btn-warning'
                            }
                        });
                        element?.focus();
                        return false;
                    }
                }

                // Validar email
                const email = document.getElementById('wizard_representante_email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Email Inválido',
                        text: 'Por favor, ingresa un email válido.',
                        confirmButtonText: 'Entendido',
                        customClass: {
                            confirmButton: 'btn btn-warning'
                        }
                    });
                    email.focus();
                    return false;
                }

                return true;
            }

            // Event listeners
            btnSubmit?.addEventListener('click', function(e) {
                e.preventDefault();
                if (validateForm()) {
                    submitForm(false);
                }
            });

            btnDemo?.addEventListener('click', function(e) {
                e.preventDefault();
                if (validateForm()) {
                    // Seleccionar automáticamente el plan demo
                    const demoRadio = document.getElementById('plan_demo');
                    if (demoRadio) {
                        demoRadio.checked = true;
                    }
                    submitForm(true);
                }
            });

            // Prevenir envío tradicional del formulario
            form.addEventListener('submit', function(e) {
                e.preventDefault();
            });
        });
    </script>
@endsection

@section('ui-vendor-styles')

    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/@form-validation/form-validation.css') }}" />

    <style>
        /* Estilos para cards demo elegantes */
        .demo-highlight-card {
            background: linear-gradient(135deg, #696cff 0%, #5a5fe7 100%) !important;
            border: none !important;
            box-shadow: 0 8px 32px rgba(105, 108, 255, 0.3);
            overflow: hidden;
            position: relative;
            animation: demoGlow 3s ease-in-out infinite alternate;
        }
        
        .demo-highlight-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }
        
        @keyframes demoGlow {
            0% { box-shadow: 0 8px 32px rgba(105, 108, 255, 0.3); }
            100% { box-shadow: 0 12px 40px rgba(105, 108, 255, 0.5); }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        .demo-personal-card {
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
            border: 2px solid #e3f2fd !important;
            box-shadow: 0 4px 20px rgba(33, 150, 243, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .demo-personal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(33, 150, 243, 0.05), transparent);
            animation: slidePersonal 4s infinite;
        }
        
        @keyframes slidePersonal {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }
        
        .demo-icon-container {
            position: relative;
        }
        
        .demo-pulse {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            border: 2px solid rgba(40, 167, 69, 0.4);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { 
                transform: translate(-50%, -50%) scale(0.8); 
                opacity: 1; 
            }
            100% { 
                transform: translate(-50%, -50%) scale(1.5); 
                opacity: 0; 
            }
        }
        
        .demo-status {
            animation: statusBlink 2s infinite alternate;
        }
        
        @keyframes statusBlink {
            0% { opacity: 0.8; }
            100% { opacity: 1; }
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        }
        
        .plan-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .plan-card.selected {
            border-color: var(--bs-primary) !important;
            box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb), 0.25);
        }
        
        .demo-card {
            transition: all 0.3s ease;
            cursor: pointer;
            background: linear-gradient(135deg, #fff3cd 0%, #ffffff 100%);
        }
        
        .demo-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.2);
        }
        
        .demo-card.selected {
            border-color: var(--bs-warning) !important;
            box-shadow: 0 0 0 2px rgba(var(--bs-warning-rgb), 0.25);
            background: linear-gradient(135deg, #fff3cd 0%, #fef3cd 100%);
        }
        
        /* Loading spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .demo-highlight-card .row,
            .demo-personal-card .row {
                text-align: center;
            }
            
            .demo-highlight-card .col-md-2,
            .demo-highlight-card .col-md-8,
            .demo-highlight-card .col-md-2,
            .demo-personal-card .col-md-2,
            .demo-personal-card .col-md-8,
            .demo-personal-card .col-md-2 {
                margin-bottom: 1rem;
            }
        }
    </style>
@endsection

@section('ui-page-scripts')
    <script src="{{ asset('vuexy/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/@form-validation/auto-focus.js') }}"></script>

    <script src="{{ asset('vuexy/js/form-wizard-numbered.js') }}"></script>
    <script src="{{ asset('vuexy/js/form-wizard-validation.js') }}"></script>
@endsection
