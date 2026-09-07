@if ($entries->hasPages())
    <nav class="fc-pagination features-reveal features-reveal--up" aria-label="{{ $category['name'] }} pages">
        <div class="fc-pagination-rule" aria-hidden="true"></div>

        <p class="fc-pagination-meta">
            Page {{ $entries->currentPage() }} of {{ $entries->lastPage() }}
            <span aria-hidden="true">·</span>
            {{ $entries->total() }}
            {{ $entries->total() === 1 ? 'feature' : 'features' }}
        </p>

        <ul class="fc-pagination-list">
            <li>
                @if ($entries->onFirstPage())
                    <span class="fc-page-link fc-page-link--edge is-disabled" aria-disabled="true">Prev</span>
                @else
                    <a href="{{ $entries->previousPageUrl() }}" class="fc-page-link fc-page-link--edge" rel="prev">Prev</a>
                @endif
            </li>

            @for ($page = 1; $page <= $entries->lastPage(); $page++)
                <li>
                    @if ($page === $entries->currentPage())
                        <span class="fc-page-link is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $entries->url($page) }}" class="fc-page-link">{{ $page }}</a>
                    @endif
                </li>
            @endfor

            <li>
                @if ($entries->hasMorePages())
                    <a href="{{ $entries->nextPageUrl() }}" class="fc-page-link fc-page-link--edge" rel="next">Next</a>
                @else
                    <span class="fc-page-link fc-page-link--edge is-disabled" aria-disabled="true">Next</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
