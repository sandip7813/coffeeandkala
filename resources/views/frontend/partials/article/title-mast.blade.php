<header class="article-mast">
    <span class="article-mast-rule" aria-hidden="true"></span>
    <h1 class="article-title">{{ $article['title'] }}</h1>
    @if (filled($article['subtitle'] ?? null))
        <p class="article-subtitle">{{ $article['subtitle'] }}</p>
    @endif
    <span class="article-mast-rule" aria-hidden="true"></span>
</header>
