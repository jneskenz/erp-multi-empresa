
<!-- breadcrumbs|start -->
{{-- como optener esos datos aqui  --}}
<div
    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-2 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
        <h5 class="mb-1">
            <button type="button" class="btn btn-label-secondary rounded btn-xs px-1 me-1 waves-effect"
                title="{{ $items['description'] }}">
                <span class="alert-icon"><i class="{{ $items['icon'] }}"></i></span>
            </button>
            <span style="padding-top:2px">{{ $items['title'] }}</span>
        </h5>
        <div class="page-title-right mb-1" style="opacity: 0.7;">
            <ol class="breadcrumb m-0">
                @foreach ($items['items'] as $item)
                <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                    <a href="{{ $item['url'] }}">{{ $item['name'] }}</a>
                </li>
                @endforeach
            </ol>
        </div>        
    </div>
    <div class="d-flex align-content-center flex-wrap gap-4">
        {{ $extra ?? '' }}
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            {{ $acciones ?? '' }}
        </div>
    </div>
</div>
<!-- breadcrumbs|end -->
