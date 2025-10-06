{{-- headerCard|start --}}

<div class="card-header">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div class="d-flex align-items-center">
            <div class="badge rounded {{ $dataHeaderCard['iconColor'] ?? 'bg-label-info' }} me-4 p-2"><i class="{{ $dataHeaderCard['icon'] ?? 'ti ti-question' }}"></i></div>
            <div class="card-info">
                <h5 class="mb-0">{{ $dataHeaderCard['title'] }}</h5>
                <small class="{{ $dataHeaderCard['textColor'] ?? 'text-muted' }}">{!! $dataHeaderCard['description'] !!}</small>
            </div>
        </div>
        <div class="d-flex align-content-center flex-wrap gap-3 my-1 my-md-0">
            <div class="d-flex gap-3">
                @if ($dataHeaderCard['actions'] ?? false)
                    @foreach ($dataHeaderCard['actions'] as $action)
                        @if($action['typeAction'] == 'btnLink')
                            @can($action['permission'])
                                <a href="{{ $action['url'] ?? 'javascript:void(0)' }}"
                                    class="btn {{ $action['typeButton'] ?? 'btn-primary' }} waves-effect">
                                    <i class="{{ $action['icon'] }} me-2"></i>
                                    {{ $action['name'] }}
                                </a>
                            @endcan
                        @elseif($action['typeAction'] == 'btnToggle')
                            @can($action['permission'])
                                <button type="button" class="btn {{ $action['typeButton'] ?? 'btn-primary' }} waves-effect" data-bs-toggle="modal" data-bs-target="#{{ $action['idModal'] }}">
                                    <i class="{{ $action['icon'] }} me-2"></i>
                                    {{ $action['name'] }}
                                </button>
                            @endcan
                        @elseif($action['typeAction'] == 'btnOnClick')
                            <button type="button" class="btn {{ $action['typeButton'] ?? 'btn-primary' }} waves-effect" onclick="{{ $action['onClickFunction'] ?? '' }}">
                                <i class="{{ $action['icon'] }} me-2"></i>
                                {{ $action['name'] }}
                            </button>
                        @else
                            @can($action['permission'])
                                <a href="{{ $action['url'] ?? 'javascript:void(0)' }}"
                                    class="btn {{ $action['typeButton'] ?? 'btn-primary' }} waves-effect">
                                    <i class="{{ $action['icon'] }} me-2"></i>
                                    {{ $action['name'] }}
                                </a>
                            @endcan
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

{{-- headerCard|end --}}
