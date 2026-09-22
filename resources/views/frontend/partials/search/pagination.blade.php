@if ($paginator->hasPages())
    <nav class="search-pagination" aria-label="{{ $label }} pages">
        <ul class="search-pagination-list">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="search-page-link search-page-link--edge is-disabled" aria-disabled="true">Prev</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}#search-{{ $sectionKey }}" class="search-page-link search-page-link--edge" rel="prev">Prev</a>
                @endif
            </li>

            @for ($page = 1; $page <= $paginator->lastPage(); $page++)
                <li>
                    @if ($page === $paginator->currentPage())
                        <span class="search-page-link is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $paginator->url($page) }}#search-{{ $sectionKey }}" class="search-page-link">{{ $page }}</a>
                    @endif
                </li>
            @endfor

            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}#search-{{ $sectionKey }}" class="search-page-link search-page-link--edge" rel="next">Next</a>
                @else
                    <span class="search-page-link search-page-link--edge is-disabled" aria-disabled="true">Next</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
