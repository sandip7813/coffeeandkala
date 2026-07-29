{{-- Global Chapters — world route / destination chapters --}}
@include('frontend.partials.features.category-banner', ['category' => $category])

<div class="fc-global-wrap">
    <nav class="fc-crumb features-reveal features-reveal--up" aria-label="Breadcrumb">
        <a href="{{ route('features') }}">Features</a>
        <span aria-hidden="true">/</span>
        <span>{{ $category['name'] }}</span>
    </nav>

    <header class="fc-global-mast features-reveal features-reveal--up" aria-labelledby="fcGlobalTitle">
        <div class="fc-global-mast-copy">
            <p class="fc-global-kicker">{{ $category['eyebrow'] }} · World route</p>
            <h1 id="fcGlobalTitle">{{ $category['name'] }}</h1>
            <p class="fc-global-tagline">{{ $category['tagline'] }}</p>
            <p class="fc-global-lead">{{ $category['lead'] }}</p>
        </div>
        <ul class="fc-global-motifs">
            @foreach ($category['motifs'] as $motif)
                <li>
                    <i class="fa-solid {{ $motif['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $motif['label'] }}</span>
                </li>
            @endforeach
        </ul>
    </header>

    <section class="fc-global-route" aria-label="Articles in {{ $category['name'] }}">
        <div class="fc-global-spine" aria-hidden="true"></div>

        @foreach ($category['articles'] as $article)
            <article
                @class([
                    'fc-global-stop',
                    'fc-global-stop--alt' => $loop->even,
                    'features-reveal',
                    'features-reveal--up',
                ])
                style="--portal-delay: {{ $loop->index * 0.08 }}s"
            >
                <div class="fc-global-pin" aria-hidden="true">
                    <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <i class="fa-solid fa-location-dot"></i>
                </div>

                <a href="{{ $article['href'] }}" class="fc-global-chapter">
                    <div class="fc-global-chapter-media">
                        <img src="{{ $article['image'] }}" alt="" loading="lazy" width="860" height="520" decoding="async">
                    </div>
                    <div class="fc-global-chapter-body">
                        <div class="fc-global-meta">
                            <span>{{ $article['tag'] }}</span>
                            <time datetime="{{ $article['date'] }}">{{ $article['date_label'] }}</time>
                        </div>
                        <h2>{{ $article['title'] }}</h2>
                        <p>{{ $article['excerpt'] }}</p>
                        <span class="fc-global-cta">
                            Open chapter
                            <i class="fa-solid fa-passport" aria-hidden="true"></i>
                        </span>
                    </div>
                </a>
            </article>
        @endforeach
    </section>
</div>

@include('frontend.partials.features.theme-footer', [
    'category' => $category,
    'categories' => $categories,
])
