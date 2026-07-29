{{-- Vineyard Tales — sommelier tasting flight (vertical bottle labels) --}}
@include('frontend.partials.features.category-banner', ['category' => $category])

<div class="fc-vine-wrap">
    <nav class="fc-crumb features-reveal features-reveal--up" aria-label="Breadcrumb">
        <a href="{{ route('features') }}">Features</a>
        <span aria-hidden="true">/</span>
        <span>{{ $category['name'] }}</span>
    </nav>

    <header class="fc-vine-mast features-reveal features-reveal--up" aria-labelledby="fcVineTitle">
        <div class="fc-vine-cork" aria-hidden="true">
            <i class="fa-solid fa-wine-bottle"></i>
        </div>
        <p class="fc-vine-kicker">{{ $category['eyebrow'] }} · Sommelier’s flight</p>
        <h1 id="fcVineTitle">{{ $category['name'] }}</h1>
        <p class="fc-vine-tagline">{{ $category['tagline'] }}</p>
        <p class="fc-vine-lead">{{ $category['lead'] }}</p>

        <ul class="fc-vine-motifs">
            @foreach ($category['motifs'] as $motif)
                <li>
                    <i class="fa-solid {{ $motif['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $motif['label'] }}</span>
                </li>
            @endforeach
        </ul>
    </header>

    <section class="fc-vine-flight" aria-label="Articles in {{ $category['name'] }}">
        <p class="fc-vine-flight-label" aria-hidden="true">
            <span></span>
            This flight
            <span></span>
        </p>

        <div class="fc-vine-rack">
            @foreach ($category['articles'] as $article)
                <article
                    class="fc-vine-pour features-reveal features-reveal--up"
                    style="--portal-delay: {{ $loop->index * 0.08 }}s; --pour-index: {{ $loop->iteration }}"
                >
                    <a href="{{ $article['href'] }}" class="fc-vine-pour-link">
                        <span class="fc-vine-foil" aria-hidden="true"></span>
                        <div class="fc-vine-neck">
                            <span class="fc-vine-year">{{ \Illuminate\Support\Str::of($article['date'])->substr(0, 4) }}</span>
                            <span class="fc-vine-tag">{{ $article['tag'] }}</span>
                        </div>
                        <div class="fc-vine-glass">
                            <img src="{{ $article['image'] }}" alt="" loading="lazy" width="480" height="640" decoding="async">
                        </div>
                        <div class="fc-vine-labelcard">
                            <h2>{{ $article['title'] }}</h2>
                            <p>{{ $article['excerpt'] }}</p>
                            <time datetime="{{ $article['date'] }}">{{ $article['date_label'] }}</time>
                            <span class="fc-vine-cta">
                                Pour the story
                                <i class="fa-solid fa-wine-glass" aria-hidden="true"></i>
                            </span>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </section>
</div>

@include('frontend.partials.features.theme-footer', [
    'category' => $category,
    'categories' => $categories,
])
