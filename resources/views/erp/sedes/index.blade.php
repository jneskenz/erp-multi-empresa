@extends('layouts.vuexy')

@section('title', 'Gestión de Sedes - ERP Multisoft')

@php

    $dataBreadcrumb = [
        'title' => 'Gestión de Sede',
        'description' => 'Administra las sedes del sistema',
        'icon' => 'ti ti-building-bank',
        'breadcrumbs' => [
            ['name' => 'Config. Administrativa', 'url' => route('home')],
            ['name' => 'Sedes', 'url' => route('sedes.index'), 'active' => true],
        ],
        'stats' => [
            [
                'name' => 'Total Sedes',
                'value' => $sedes->count(),
                'icon' => 'ti ti-building',
                'color' => 'bg-label-primary',
            ],
            [
                'name' => 'Sedes Activas',
                'value' => $sedes->where('estado', true)->count(),
                'icon' => 'ti ti-circle-check',
                'color' => 'bg-label-success',
            ],
        ],
    ];

    $dataHeaderCard = [
        'title' => 'Lista de Sedes',
        'description' => '',
        'textColor' => 'text-primary',
        'icon' => 'ti ti-building-bank',
        'iconColor' => 'bg-label-primary',
        'actions' => [
            [
                'typeAction' => 'btnLink', // btnIdEvent, btnLink, btnToggle, btnInfo
                'typeButton' => 'btn-primary', // btn-primary, btn-info, btn-success, btn-danger, btn-warning, btn-secondary
                'name' => 'Crear Sede',
                'url' => route('sedes.create'),
                'icon' => 'ti ti-plus',
                'permission' => 'sedes.create',
            ],
        ],
    ];


@endphp


@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        @include('layouts.vuexy.breadcrumb', $dataBreadcrumb)

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header align-items-center justify-content-between border-bottom">
                        <div class="nav-align-top">
                            <ul class="nav nav-pills flex-column flex-md-row">
                                <li class="nav-item mb-2 mb-md-0 me-0 me-md-3">
                                    <a class="nav-link border" href="{{ route('empresas.index') }}">
                                        <i class="ti-xs ti ti-building me-1"></i>
                                        Empresas
                                    </a>
                                </li>
                                <li class="nav-item mb-2 mb-md-0 me-0 me-md-3">
                                    <a class="nav-link active" href="javascript:void(0);">
                                        <i class="ti-sm ti ti-building-bank me-1"></i>
                                        Sedes
                                    </a>
                                </li>
                                <li class="nav-item mb-2 mb-md-0 me-0 me-md-3">
                                    <a class="nav-link border" href="pages-account-settings-billing.html">
                                        <i class="ti-xs ti ti-building-store me-1"></i>
                                        Locales
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    @include('layouts.vuexy.header-card', $dataHeaderCard)

                    <div class="card-body">
                        <!-- Componente Livewire con estilo Vuexy -->
                        @livewire('erp.sedes-data-table')
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row mt-4">
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('vuexy/img/backgrounds/speaker.png') }}" alt="sedes"
                                    class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Sedes</span>
                        <h3 class="card-title mb-2">{{ $sedes->count() }}</h3>
                        <small class="text-success fw-semibold">
                            <i class="ti ti-up-arrow-alt"></i> Registradas
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('vuexy/img/backgrounds/speaker.png') }}" alt="activas"
                                    class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Sedes Activas</span>
                        <h3 class="card-title mb-2">{{ $sedes->where('estado', true)->count() }}</h3>
                        <small class="text-info fw-semibold">
                            <i class="ti ti-check-circle"></i> Operativas
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            });
        });
    </script>
@endpush
