{{--
    Table of Contents. Links carry no hash (`javascript:void(0)` + a plain
    `data-scroll-target`, the same convention used elsewhere on the site for
    JS-driven navigation) so clicking never touches the URL — article.js
    reads data-scroll-target and scrolls smoothly to the matching section id.
    `data-toc` on the wrapper is a hook so this list can be swapped for a
    dynamically fetched one later without touching the scroll behaviour.
--}}
<nav class="article-toc" aria-label="Table of contents" data-toc>
    <div class="article-toc-head">
        <p class="article-toc-label">
            <i class="fa-solid fa-list-ul" aria-hidden="true"></i>
            Table of Contents
        </p>
        <time class="article-toc-date" datetime="{{ $article['date'] }}">
            <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
            {{ $article['date_label'] }}
        </time>
    </div>
    <ol class="article-toc-list">
        @foreach ($content['toc'] as $item)
            <li>
                <a
                    href="javascript:void(0)"
                    class="article-toc-link"
                    data-toc-link
                    data-scroll-target="{{ $item['id'] }}"
                >
                    <span class="article-toc-index">{{ sprintf('%02d', $loop->iteration) }}</span>
                    <span class="article-toc-text">{{ $item['label'] }}</span>
                    <i class="fa-solid fa-arrow-right article-toc-arrow" aria-hidden="true"></i>
                </a>
            </li>
        @endforeach
    </ol>
</nav>
