{{-- Coffee & Classics — dark academia reading list / library shelf --}}
@include('frontend.partials.features.category-banner', [
    'category' => $category,
    'bannerClass' => 'fc-coffee-banner',
])

<div class="fc-coffee-room">
    <nav class="fc-crumb features-reveal features-reveal--up" aria-label="Breadcrumb">
        <a href="{{ route('features') }}">Features</a>
        <span aria-hidden="true">/</span>
        <span>{{ $category['name'] }}</span>
    </nav>

    <section class="fc-coffee-intro features-reveal features-reveal--up" aria-labelledby="fcCoffeeTitle">
        <p class="fc-coffee-kicker">{{ $category['eyebrow'] }} · Reading hour</p>
        <h1 id="fcCoffeeTitle">{{ $category['name'] }}</h1>
        <p class="fc-coffee-tagline">{{ $category['tagline'] }}</p>
        <p class="fc-coffee-lead">{{ $category['lead'] }}</p>

        <ul class="fc-coffee-motifs">
            @foreach ($category['motifs'] as $motif)
                <li>
                    <i class="fa-solid {{ $motif['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $motif['label'] }}</span>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="fc-coffee-shelf" aria-label="Articles in {{ $category['name'] }}">
        <p class="fc-coffee-shelf-label" aria-hidden="true">On this shelf</p>
        @foreach ($category['articles'] as $article)
            <article class="fc-coffee-spine features-reveal features-reveal--up" style="--portal-delay: {{ $loop->index * 0.1 }}s">
                <a href="{{ $article['href'] }}">
                    <div class="fc-coffee-thumb">
                        <img src="{{ $article['image'] }}" alt="" loading="lazy" width="320" height="420" decoding="async">
                    </div>
                    <div class="fc-coffee-copy">
                        <div class="fc-coffee-meta">
                            <span>{{ $article['tag'] }}</span>
                            <time datetime="{{ $article['date'] }}">{{ $article['date_label'] }}</time>
                        </div>
                        <h2>{{ $article['title'] }}</h2>
                        <p>{{ $article['excerpt'] }}</p>
                        <span class="fc-coffee-cta">Open chapter <i class="fa-solid fa-bookmark" aria-hidden="true"></i></span>
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
