{{-- On a Budget — notebook checklist / practical planner --}}
<nav class="fc-crumb features-reveal features-reveal--up" aria-label="Breadcrumb">
    <a href="{{ route('features') }}">Features</a>
    <span aria-hidden="true">/</span>
    <span>{{ $category['name'] }}</span>
</nav>

<section class="fc-budget-sheet features-reveal features-reveal--up" aria-labelledby="fcBudgetTitle">
    <div class="fc-budget-ruled">
        <p class="fc-budget-kicker">{{ $category['eyebrow'] }} · Travel checklist</p>
        <h1 id="fcBudgetTitle">{{ $category['name'] }}</h1>
        <p class="fc-budget-tagline">{{ $category['tagline'] }}</p>
        <p class="fc-budget-lead">{{ $category['lead'] }}</p>
    </div>
</section>

<section class="fc-budget-grid" aria-label="Articles in {{ $category['name'] }}">
    @foreach ($category['articles'] as $article)
        <article class="fc-budget-card features-reveal features-reveal--up" style="--portal-delay: {{ $loop->index * 0.08 }}s">
            <a href="{{ $article['href'] }}">
                <div class="fc-budget-card-media">
                    <img src="{{ $article['image'] }}" alt="" loading="lazy" width="700" height="480" decoding="async">
                    <span class="fc-budget-pin" aria-hidden="true">{{ $article['tag'] }}</span>
                </div>
                <div class="fc-budget-card-body">
                    <time datetime="{{ $article['date'] }}">{{ $article['date_label'] }}</time>
                    <h2>{{ $article['title'] }}</h2>
                    <p>{{ Str::limit($article['excerpt'], 80) }}</p>
                    <span class="fc-budget-cta">Open tip sheet →</span>
                </div>
            </a>
        </article>
    @endforeach
</section>

@include('frontend.partials.features.theme-footer', [
    'category' => $category,
    'categories' => $categories,
])
