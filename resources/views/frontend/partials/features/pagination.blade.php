<nav class="fc-pagination features-reveal features-reveal--up" aria-label="{{ $category['name'] }} pages">
    <div class="fc-pagination-rule" aria-hidden="true"></div>

    <p class="fc-pagination-meta">
        Page 1 of 3
        <span aria-hidden="true">·</span>
        {{ count($category['articles']) }}
        {{ count($category['articles']) === 1 ? 'feature' : 'features' }}
    </p>

    <ul class="fc-pagination-list">
        <li>
            <span class="fc-page-link fc-page-link--edge is-disabled" aria-disabled="true">Prev</span>
        </li>
        <li>
            <span class="fc-page-link is-current" aria-current="page">1</span>
        </li>
        <li>
            <a href="javascript:void(0)" class="fc-page-link">2</a>
        </li>
        <li>
            <a href="javascript:void(0)" class="fc-page-link">3</a>
        </li>
        <li>
            <a href="javascript:void(0)" class="fc-page-link fc-page-link--edge" rel="next">Next</a>
        </li>
    </ul>
</nav>
