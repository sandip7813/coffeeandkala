<nav class="article-crumb" aria-label="Breadcrumb">
    <a href="{{ route('home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a>
    <span class="article-crumb-sep" aria-hidden="true">/</span>
    <a href="{{ $sourceIndexHref }}">{{ $sourceLabel }}</a>
    <span class="article-crumb-sep" aria-hidden="true">/</span>
    <a href="{{ $categoryHref }}">{{ $category['name'] }}</a>
    <span class="article-crumb-sep" aria-hidden="true">/</span>
    <span class="article-crumb-current" aria-current="page">{{ $article['title'] }}</span>
</nav>
