@if ($entries->hasPages())
    <nav class="jc-pagination journal-reveal journal-reveal--up" aria-label="{{ $category['name'] }} pages">
        <div class="jc-pagination-rule" aria-hidden="true"></div>

        <p class="jc-pagination-meta">
            Page {{ $entries->currentPage() }} of {{ $entries->lastPage() }}
            <span aria-hidden="true">·</span>
            {{ $entries->total() }} {{ $entries->total() === 1 ? 'dispatch' : 'dispatches' }}
        </p>

        <ul class="jc-pagination-list">
            <li>
                @if ($entries->onFirstPage())
                    <span class="jc-page-link jc-page-link--edge is-disabled" aria-disabled="true">Prev</span>
                @else
                    <a href="{{ $entries->previousPageUrl() }}" class="jc-page-link jc-page-link--edge" rel="prev">Prev</a>
                @endif
            </li>

            @for ($page = 1; $page <= $entries->lastPage(); $page++)
                <li>
                    @if ($page === $entries->currentPage())
                        <span class="jc-page-link is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $entries->url($page) }}" class="jc-page-link">{{ $page }}</a>
                    @endif
                </li>
            @endfor

            <li>
                @if ($entries->hasMorePages())
                    <a href="{{ $entries->nextPageUrl() }}" class="jc-page-link jc-page-link--edge" rel="next">Next</a>
                @else
                    <span class="jc-page-link jc-page-link--edge is-disabled" aria-disabled="true">Next</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
