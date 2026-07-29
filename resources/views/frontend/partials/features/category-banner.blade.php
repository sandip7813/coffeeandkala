{{-- Category banner with full-image zoom (same pattern as Features / About) --}}
<section
    @class([
        'fc-banner',
        'about-banner',
        'features-reveal',
        $bannerClass ?? null,
    ])
    id="categoryBanner"
    aria-label="{{ $category['name'] }} banner"
>
    <div class="about-banner-frame">
        <img
            src="{{ asset($category['banner']) }}"
            alt="{{ $category['name'] }} — {{ $category['tagline'] }}"
            class="about-banner-image fc-banner-img"
            width="1600"
            height="900"
            decoding="async"
            fetchpriority="high"
        >
        <div class="about-banner-veil fc-banner-veil" aria-hidden="true"></div>
        <button
            type="button"
            class="about-banner-zoom"
            id="categoryBannerZoom"
            aria-label="Show full banner image"
            aria-expanded="false"
            aria-controls="categoryBanner"
            title="Show full image"
        >
            <i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i>
        </button>
    </div>
</section>
