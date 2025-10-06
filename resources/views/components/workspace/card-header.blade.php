{{-- headerCard|start --}}
@php

if(isset($estado)){
    $btnColorMatch = match($estado) {
        '1' => 'btn-label-success',
        '0' => 'btn-label-warning',
        '5' => 'btn-label-danger',
        default => 'btn-label-secondary',
    };
    
    $btnTextMatch = match($estado) {
        '1' => 'Activo',
        '0' => 'Inactivo',
        '5' => 'Eliminado',
        default => 'Desconocido',
    };
    
    $btnIconMatch = match($estado) {
        '1' => 'ti ti-check',
        '0' => 'ti ti-alert-triangle',
        '5' => 'ti ti-circle-x',
        default => 'ti ti-help',
    };
}


@endphp

<div class="card-header">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div class="d-flex align-items-center">
            <div class="badge rounded {{ $iconColor ?? 'bg-label-info' }} me-4 p-2"><i class="{{ $icon ?? 'ti ti-question' }}"></i></div>
            <div class="card-info">
                <h5 class="mb-0">{{ $title }}</h5>
                <small class="{{ $textColor ?? 'text-muted' }}">{!! $description !!}</small>
            </div>
        </div>
        <div class="d-flex align-content-center flex-wrap gap-3 my-1 my-md-0">
            @if(isset($estado))
                <a href="javascript:void(0)" class="btn {{ $btnColorMatch }} waves-effect">
                    <i class="{{ $btnIconMatch }} me-2"></i>
                    {{ $btnTextMatch }}
                </a>
            @endif
            <div class="d-flex gap-3">

                {{ $slot ?? '' }}
                
            </div>
        </div>
    </div>
</div>

{{-- headerCard|end --}}
