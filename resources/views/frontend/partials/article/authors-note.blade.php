<footer id="authors-note" class="article-authors-note" aria-label="Author's Note">
    <p class="article-authors-note-label">Author&rsquo;s Note</p>

    <div class="article-authors-note-row">
        <span class="article-authors-note-avatar" aria-hidden="true">
            <img
                src="{{ \App\Support\BrandLogo::url() }}"
                alt=""
                width="184"
                height="184"
                loading="lazy"
                decoding="async"
            >
        </span>

        <div class="article-authors-note-copy">
            <div class="article-authors-note-body">
                {!! $content['authors_note']['body'] !!}
            </div>
        </div>
    </div>
</footer>
