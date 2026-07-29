{{-- Banner sits in document flow directly under the top nav --}}
<section class="about-banner" id="aboutBanner" aria-label="Our Story banner">
    <div class="about-banner-frame">
        <img
            src="{{ asset('images/about/banner.png') }}"
            alt="Brewing Art & Words, One Cup At A Time — a desk with typewriter, coffee, journal, and paint"
            class="about-banner-image"
            width="1024"
            height="585"
        >
        <div class="about-banner-veil" aria-hidden="true"></div>
        <div class="about-banner-caption about-reveal about-reveal--copy">
            <p class="about-eyebrow about-eyebrow--on-dark">Our Story</p>
            <h1 class="about-banner-title">Coffee &amp; Kala</h1>
            <p class="about-banner-lead">Brewing art &amp; words, one cup at a time.</p>
        </div>
        <button
            type="button"
            class="about-banner-zoom"
            id="aboutBannerZoom"
            aria-label="Show full banner image"
            aria-expanded="false"
            aria-controls="aboutBanner"
            title="Show full image"
        >
            <i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i>
        </button>
    </div>
</section>

<div class="about-banner-divider" aria-hidden="true">
    <span class="about-banner-divider-line"></span>
</div>
