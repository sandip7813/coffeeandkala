{{-- Not On The Atlas — scrapbook journal / taped field notes --}}
<nav class="fc-crumb features-reveal features-reveal--up" aria-label="Breadcrumb">
    <a href="{{ route('features') }}">Features</a>
    <span aria-hidden="true">/</span>
    <span>{{ $category['name'] }}</span>
</nav>

<section class="fc-atlas-intro features-reveal features-reveal--up" aria-labelledby="fcAtlasTitle">
    <p class="fc-atlas-note" aria-hidden="true">Field journal · {{ $category['number'] }}</p>
    <h1 id="fcAtlasTitle">{{ $category['name'] }}</h1>
    <p class="fc-atlas-tagline">{{ $category['tagline'] }}</p>
    <p class="fc-atlas-lead">{{ $category['lead'] }}</p>
</section>

<section class="fc-atlas-scrapbook" aria-label="Articles in {{ $category['name'] }}">
    @foreach ($category['articles'] as $article)
        <article
            @class([
                'fc-atlas-clip',
                'fc-atlas-clip--'.$loop->iteration,
                'features-reveal',
                'features-reveal--up',
            ])
            style="--portal-delay: {{ $loop->index * 0.1 }}s"
        >
            <span class="fc-atlas-tape" aria-hidden="true"></span>
            <a href="{{ $article['href'] }}">
                <div class="fc-atlas-media">
                    <img src="{{ $article['image'] }}" alt="" loading="lazy" width="800" height="560" decoding="async">
                </div>
                <div class="fc-atlas-body">
                    <span class="fc-atlas-tag">{{ $article['tag'] }}</span>
                    <h2>{{ $article['title'] }}</h2>
                    <p>{{ Str::limit($article['excerpt'], 500) }}</p>
                    <time datetime="{{ $article['date'] }}">{{ $article['date_label'] }}</time>
                </div>
            </a>
        </article>
    @endforeach
</section>

@include('frontend.partials.features.theme-footer', [
    'category' => $category,
    'categories' => $categories,
])
