<section class="features-category-hero" aria-labelledby="featuresCategoryTitle">
    <div
        class="features-category-hero-media features-reveal features-reveal--up"
        style="background-image: url('{{ $category['cover'] }}')"
        role="img"
        aria-label="{{ $category['name'] }}"
    >
        <div class="features-category-hero-veil" aria-hidden="true"></div>
    </div>

    <div class="features-category-hero-copy features-reveal features-reveal--up">
        <nav class="features-category-crumb" aria-label="Breadcrumb">
            <a href="{{ route('features') }}">Features</a>
            <span aria-hidden="true">/</span>
            <span>{{ $category['name'] }}</span>
        </nav>

        <p class="features-eyebrow">
            <i class="fa-solid {{ $category['icon'] }}" aria-hidden="true"></i>
            {{ $category['eyebrow'] }}
        </p>
        <h1 id="featuresCategoryTitle" class="features-category-title">{{ $category['name'] }}</h1>
        <span class="features-ornament" aria-hidden="true"></span>
        <p class="features-category-lead">{{ $category['lead'] }}</p>
        <p class="features-intro-aside">
            {{ count($category['articles']) }}
            {{ count($category['articles']) === 1 ? 'feature' : 'features' }} in this chapter
        </p>
    </div>
</section>
