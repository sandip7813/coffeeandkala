@if ($poems->hasPages())
    <nav class="poetry-pagination poetry-reveal poetry-reveal--up" aria-label="Poem pages">
        <div class="poetry-pagination-rule" aria-hidden="true"></div>

        <p class="poetry-pagination-meta">
            Page {{ $poems->currentPage() }} of {{ $poems->lastPage() }}
            <span aria-hidden="true">·</span>
            {{ $poems->total() }} {{ $poems->total() === 1 ? 'poem' : 'poems' }}
        </p>

        <ul class="poetry-pagination-list">
            <li>
                @if ($poems->onFirstPage())
                    <span class="poetry-page-link poetry-page-link--edge is-disabled" aria-disabled="true">Prev</span>
                @else
                    <a href="{{ $poems->previousPageUrl() }}" class="poetry-page-link poetry-page-link--edge" rel="prev">Prev</a>
                @endif
            </li>

            @for ($page = 1; $page <= $poems->lastPage(); $page++)
                <li>
                    @if ($page === $poems->currentPage())
                        <span class="poetry-page-link is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $poems->url($page) }}" class="poetry-page-link">{{ $page }}</a>
                    @endif
                </li>
            @endfor

            <li>
                @if ($poems->hasMorePages())
                    <a href="{{ $poems->nextPageUrl() }}" class="poetry-page-link poetry-page-link--edge" rel="next">Next</a>
                @else
                    <span class="poetry-page-link poetry-page-link--edge is-disabled" aria-disabled="true">Next</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
