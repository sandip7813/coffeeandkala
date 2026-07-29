{{-- Experiences — travel itinerary (compact stop list) --}}
@include('frontend.partials.features.category-banner', ['category' => $category])

<div class="fc-exp-wrap">
    <nav class="fc-crumb features-reveal features-reveal--up" aria-label="Breadcrumb">
        <a href="{{ route('features') }}">Features</a>
        <span aria-hidden="true">/</span>
        <span>{{ $category['name'] }}</span>
    </nav>

    <header class="fc-exp-mast features-reveal features-reveal--up" aria-labelledby="fcExpTitle">
        <div class="fc-exp-mast-top">
            <div>
                <p class="fc-exp-kicker">{{ $category['eyebrow'] }} · Itinerary</p>
                <h1 id="fcExpTitle">{{ $category['name'] }}</h1>
            </div>
            <p class="fc-exp-tagline">{{ $category['tagline'] }}</p>
        </div>
        <p class="fc-exp-lead">{{ $category['lead'] }}</p>
        <ul class="fc-exp-motifs">
            @foreach ($category['motifs'] as $motif)
                <li>
                    <i class="fa-solid {{ $motif['icon'] }}" aria-hidden="true"></i>
                    {{ $motif['label'] }}
                </li>
            @endforeach
        </ul>
    </header>

    <section class="fc-exp-itinerary" aria-label="Articles in {{ $category['name'] }}">
        @foreach ($category['articles'] as $article)
            <article class="fc-exp-leg features-reveal features-reveal--up" style="--portal-delay: {{ $loop->index * 0.06 }}s">
                <a href="{{ $article['href'] }}" class="fc-exp-leg-link">
                    <div class="fc-exp-stop-col">
                        <span class="fc-exp-stop-num">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="fc-exp-stop-label">Stop</span>
                    </div>
                    <div class="fc-exp-leg-media">
                        <img src="{{ $article['image'] }}" alt="" loading="lazy" width="280" height="200" decoding="async">
                    </div>
                    <div class="fc-exp-leg-body">
                        <div class="fc-exp-meta">
                            <span>{{ $article['tag'] }}</span>
                            <time datetime="{{ $article['date'] }}">{{ $article['date_label'] }}</time>
                        </div>
                        <h2>{{ $article['title'] }}</h2>
                        <p>{{ $article['excerpt'] }}</p>
                    </div>
                    <span class="fc-exp-go" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></span>
                </a>
            </article>
        @endforeach
    </section>
</div>

@include('frontend.partials.features.theme-footer', [
    'category' => $category,
    'categories' => $categories,
])
