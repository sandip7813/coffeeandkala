{{-- Luxury Escapes — cinematic resort lookbook --}}
<div class="fc-lux-wrap">
    <nav class="fc-crumb features-reveal features-reveal--up" aria-label="Breadcrumb">
        <a href="{{ route('features') }}">Features</a>
        <span aria-hidden="true">/</span>
        <span>{{ $category['name'] }}</span>
    </nav>

    <header class="fc-lux-mast features-reveal features-reveal--up" aria-labelledby="fcLuxTitle">
        <p class="fc-lux-kicker">{{ $category['eyebrow'] }} · Resort lookbook</p>
        <h1 id="fcLuxTitle">{{ $category['name'] }}</h1>
        <span class="fc-lux-diamond" aria-hidden="true"></span>
        <p class="fc-lux-tagline">{{ $category['tagline'] }}</p>
        <p class="fc-lux-lead">{{ $category['lead'] }}</p>
        <ul class="fc-lux-motifs">
            @foreach ($category['motifs'] as $motif)
                <li>
                    <i class="fa-solid {{ $motif['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $motif['label'] }}</span>
                </li>
            @endforeach
        </ul>
    </header>

    <section class="fc-lux-lookbook" aria-label="Articles in {{ $category['name'] }}">
        @foreach ($category['articles'] as $article)
            <article
                @class([
                    'fc-lux-panel',
                    'fc-lux-panel--hero' => $loop->first,
                    'features-reveal',
                    'features-reveal--up',
                ])
                style="--portal-delay: {{ $loop->index * 0.08 }}s"
            >
                <a href="{{ $article['href'] }}" class="fc-lux-panel-link">
                    <img
                        src="{{ $article['image'] }}"
                        alt=""
                        class="fc-lux-panel-image"
                        loading="lazy"
                        width="1400"
                        height="700"
                        decoding="async"
                    >
                    <div class="fc-lux-panel-veil" aria-hidden="true"></div>
                    <div class="fc-lux-panel-copy">
                        <div class="fc-lux-panel-meta">
                            <span class="fc-lux-panel-no">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span>{{ $article['tag'] }}</span>
                            <time datetime="{{ $article['date'] }}">{{ $article['date_label'] }}</time>
                        </div>
                        <h2>{{ $article['title'] }}</h2>
                        <p>{{ Str::limit($article['excerpt'], 500) }}</p>
                        <span class="fc-lux-cta">
                            Enter the suite
                            <i class="fa-solid fa-gem" aria-hidden="true"></i>
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
