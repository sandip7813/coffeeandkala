<div class="article-mobile-fabs">
    <button
        type="button"
        class="article-mobile-fab article-mobile-fab--left"
        id="articleExploreFab"
        aria-label="Explore the Sections"
        aria-expanded="false"
        aria-controls="articleExplorePanel"
    >
        <i class="fa-solid fa-list-ul" aria-hidden="true"></i>
    </button>
    <button
        type="button"
        class="article-mobile-fab article-mobile-fab--right"
        id="articleRecentFab"
        aria-label="Recently Published"
        aria-expanded="false"
        aria-controls="articleRecentPanel"
    >
        <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
    </button>
</div>

<div class="article-mobile-overlay" id="articleMobileOverlay"></div>

<div class="article-sidebar-panel article-mobile-panel article-mobile-panel--left" id="articleExplorePanel">
    <button type="button" class="article-mobile-panel-close" data-article-panel-close aria-label="Close Explore the Sections">
        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
    </button>

    <p class="article-sidebar-heading">Explore the Sections</p>
    <ul class="article-tree">
        @foreach ($subcategories as $branch)
            <li class="article-tree-branch">
                {{-- Only the branch the current article belongs to starts
                     expanded (e.g. Journal open, Features closed on a
                     Journal article) — either can still be toggled. --}}
                <details @if ($branch['is_current_branch']) open @endif>
                    <summary @class(['article-tree-root', 'is-current-branch' => $branch['is_current_branch']])>
                        <i class="fa-solid {{ $branch['icon'] }} article-tree-root-icon" aria-hidden="true"></i>
                        <span>{{ $branch['name'] }}</span>
                        <i class="fa-solid fa-chevron-down article-tree-caret" aria-hidden="true"></i>
                    </summary>
                    <ul class="article-tree-children">
                        @foreach ($branch['children'] as $child)
                            <li>
                                <a href="{{ $child['href'] }}" @class(['article-tree-link', 'is-current' => $child['is_current']])>
                                    {{ $child['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </details>
            </li>
        @endforeach
    </ul>
</div>

<div class="article-sidebar-panel article-mobile-panel article-mobile-panel--right" id="articleRecentPanel">
    <button type="button" class="article-mobile-panel-close" data-article-panel-close aria-label="Close Recently Published">
        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
    </button>

    <p class="article-sidebar-heading">Recently Published</p>
    <ul class="article-sidebar-recent">
        @foreach ($recent as $item)
            <li>
                <a href="{{ $item['href'] }}" class="article-sidebar-recent-link">
                    <span
                        class="article-sidebar-recent-media"
                        style="background-image: url('{{ $item['image'] }}')"
                        role="img"
                        aria-label="{{ $item['title'] }}"
                    ></span>
                    <span class="article-sidebar-recent-body">
                        <span class="article-sidebar-recent-tag">{{ $item['category_name'] }}</span>
                        <span class="article-sidebar-recent-title">{{ $item['title'] }}</span>
                        <time datetime="{{ $item['date'] }}">{{ $item['date_label'] }}</time>
                    </span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
