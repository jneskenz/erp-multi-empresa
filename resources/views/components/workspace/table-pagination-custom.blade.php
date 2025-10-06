@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation" role="navigation">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            {{-- Información de página --}}
            <div class="text-muted">
                <small>
                    Mostrando <strong>{{ $paginator->firstItem() }}</strong> a <strong>{{ $paginator->lastItem() }}</strong> de <strong>{{ $paginator->total() }}</strong> resultados
                </small>
            </div>

            {{-- Controles de paginación --}}
            <ul class="pagination pagination-sm mb-0">
                {{-- Botón Anterior --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <i class="ti ti-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <button type="button" class="page-link" wire:click="previousPage" rel="prev" aria-label="Anterior">
                            <i class="ti ti-chevron-left"></i>
                        </button>
                    </li>
                @endif

                {{-- Páginas numeradas (máximo 3 botones + actual) --}}
                @php
                    $current = $paginator->currentPage();
                    $last = $paginator->lastPage();
                    
                    // Lógica simplificada: mostrar página actual y vecinas
                    $start = max(1, $current - 1);
                    $end = min($last, $current + 1);
                    
                    // Ajustar si estamos en los extremos
                    if ($current == 1) {
                        $end = min($last, 3);
                    } elseif ($current == $last) {
                        $start = max(1, $last - 2);
                    }
                @endphp

                {{-- Primera página si no está visible --}}
                @if ($start > 1)
                    <li class="page-item">
                        <button type="button" class="page-link" wire:click="gotoPage(1)">1</button>
                    </li>
                    @if ($start > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                {{-- Páginas del rango visible --}}
                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $current)
                        <li class="page-item active" aria-current="page">
                            <span class="page-link fw-bold">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <button type="button" class="page-link" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                        </li>
                    @endif
                @endfor

                {{-- Última página si no está visible --}}
                @if ($end < $last)
                    @if ($end < $last - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <button type="button" class="page-link" wire:click="gotoPage({{ $last }})">{{ $last }}</button>
                    </li>
                @endif

                {{-- Botón Siguiente --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <button type="button" class="page-link" wire:click="nextPage" rel="next" aria-label="Siguiente">
                            <i class="ti ti-chevron-right"></i>
                        </button>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <i class="ti ti-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif