@php
    $previous = $neighbors['previous'] ?? null;
    $next = $neighbors['next'] ?? null;
@endphp

<nav class="article-pager" aria-label="More in {{ $category['name'] }}">
    <a
        href="{{ $previous['href'] ?? $categoryHref }}"
        @class(['article-pager-link', 'article-pager-link--prev', 'is-disabled' => $previous === null])
    >
        <span class="article-pager-arrow" aria-hidden="true"><i class="fa-solid fa-arrow-left-long"></i></span>
        <span class="article-pager-copy">
            <span class="article-pager-label">Previous</span>
            <span class="article-pager-title">{{ Str::limit($previous['title'] ?? $category['name'], 42) }}</span>
        </span>
    </a>

    <a href="{{ $categoryHref }}" class="article-pager-index" aria-label="Back to {{ $category['name'] }}">
        <i class="fa-solid fa-grip" aria-hidden="true"></i>
    </a>

    <a
        href="{{ $next['href'] ?? $categoryHref }}"
        @class(['article-pager-link', 'article-pager-link--next', 'is-disabled' => $next === null])
    >
        <span class="article-pager-copy">
            <span class="article-pager-label">Next</span>
            <span class="article-pager-title">{{ Str::limit($next['title'] ?? $category['name'], 42) }}</span>
        </span>
        <span class="article-pager-arrow" aria-hidden="true"><i class="fa-solid fa-arrow-right-long"></i></span>
    </a>
</nav>
