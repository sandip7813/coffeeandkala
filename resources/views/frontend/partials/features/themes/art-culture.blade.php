{{-- Art & Culture — exhibition catalogue (dense content index) --}}
<div class="fc-art-wrap">
    <nav class="fc-crumb features-reveal features-reveal--up" aria-label="Breadcrumb">
        <a href="{{ route('features') }}">Features</a>
        <span aria-hidden="true">/</span>
        <span>{{ $category['name'] }}</span>
    </nav>

    <header class="fc-art-mast features-reveal features-reveal--up" aria-labelledby="fcArtTitle">
        <div class="fc-art-mast-copy">
            <p class="fc-art-eyebrow">{{ $category['eyebrow'] }} · Exhibition catalogue</p>
            <h1 id="fcArtTitle">{{ $category['name'] }}</h1>
            <p class="fc-art-tagline">{{ $category['tagline'] }}</p>
        </div>
    </header>

    <section class="fc-art-catalogue" aria-label="Articles in {{ $category['name'] }}">
        <div class="fc-art-catalogue-head" aria-hidden="true">
            <span>No.</span>
            <span>Work</span>
            <span>Notes</span>
            <span>Dated</span>
        </div>

        @foreach ($category['articles'] as $article)
            <article class="fc-art-entry features-reveal features-reveal--up" style="--portal-delay: {{ $loop->index * 0.06 }}s">
                <a href="{{ $article['href'] }}" class="fc-art-entry-link">
                    <span class="fc-art-no">{{ $category['number'] }}.{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="fc-art-thumb">
                        <img src="{{ $article['image'] }}" alt="" loading="lazy" width="160" height="160" decoding="async">
                    </div>
                    <div class="fc-art-entry-body">
                        <span class="fc-art-tag">{{ $article['tag'] }}</span>
                        <h2>{{ $article['title'] }}</h2>
                        <p>{{ Str::limit($article['excerpt'], 80) }}</p>
                    </div>
                    <time datetime="{{ $article['date'] }}">{{ $article['date_label'] }}</time>
                </a>
            </article>
        @endforeach
    </section>
</div>

@include('frontend.partials.features.theme-footer', [
    'category' => $category,
    'categories' => $categories,
])
