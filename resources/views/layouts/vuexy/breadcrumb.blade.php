<!-- breadcrumbs|start -->
{{-- como optener esos datos aqui  --}}
<div
    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-2 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
        <h5 class="mb-1">
            <button type="button" class="btn btn-label-secondary rounded btn-xs px-1 me-1 waves-effect"
                title="{{ $dataBreadcrumb['description'] }}">
                <span class="alert-icon"><i class="{{ $dataBreadcrumb['icon'] }}"></i></span>
            </button>
            {{ $dataBreadcrumb['title'] }}
        </h5>
        <p class="mb-1">
            {{-- @foreach ($dataBreadcrumb['breadcrumbs'] as $breadcrumb)
                @if (!$loop->last)
                    <a href="{{ $breadcrumb['url'] }}" class="text-primary">{{ $breadcrumb['name'] }}</a>
                    <span class="text-secondary"> › </span>
                @else
                    <span class="text-secondary">{{ $breadcrumb['name'] }}</span>
                @endif
            @endforeach --}}
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    @foreach ($dataBreadcrumb['breadcrumbs'] as $breadcrumb)

                    <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a></li>

                    @endforeach
                </ol>
            </div>
        </p>
        
    </div>
    <div class="d-flex align-content-center flex-wrap gap-4">

        {{-- estatus --}}
        @if ($dataBreadcrumb['stats'] ?? false)
            @foreach ($dataBreadcrumb['stats'] as $stat)
                <div class="d-flex align-items-center">
                    <span class="badge {{ $stat['color'] }} me-2">
                        <i class="{{ $stat['icon'] }}"></i>
                    </span>
                    <span class="text-muted">{{ $stat['name'] }}: {{ $stat['value'] }}</span>
                </div>
            @endforeach
        @endif

        {{-- acciones --}}
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            @if ($dataBreadcrumb['actions'] ?? false)
                @foreach ($dataBreadcrumb['actions'] as $action)
                    @can($action['permission'])
                        {{-- agrega en la etiqueta a una clase para cuando es responsive --}}
                        <a href="{{ $action['url'] }}" class="btn {{ $action['typeButton'] ?? 'btn-primary' }} waves-effect">
                            <i class="{{ $action['icon'] }} me-2"></i>
                            {{ $action['name'] }}
                        </a>
                    @endcan
                @endforeach
            @endif

        </div>

    </div>
</div>
<!-- breadcrumbs|end -->
