@extends('layouts.vuexy')

@section('title', 'Plan Expirado')

@section('content')
<div class="container-xxl container-p-y">
    <div class="misc-wrapper text-center">
        <h2 class="mb-2 mx-2">Plan Expirado</h2>
        <p class="mb-4 mx-2">
            El plan del grupo empresarial <strong>{{ $grupo }}</strong> ha expirado.
            <br>Por favor, contacta con el administrador para renovar el plan.
        </p>
        
        <div class="mt-4">
            <img
                src="{{ asset('vuexy/img/illustrations/page-misc-error-light.png') }}"
                alt="plan-expirado"
                width="500"
                class="img-fluid"
            />
        </div>
        
        <div class="mt-4">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="ti ti-arrow-left me-1"></i>Volver al Dashboard
            </a>
            
            @if(auth()->user()->esSuperusuario() || auth()->user()->esAdministradorGeneral())
                <a href="{{ route('grupo.plan', ['grupo' => $grupo]) }}" class="btn btn-outline-primary">
                    <i class="ti ti-credit-card me-1"></i>Renovar Plan
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
