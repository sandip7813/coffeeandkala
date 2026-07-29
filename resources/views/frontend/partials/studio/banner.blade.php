{{-- Banner sits in document flow directly under the top nav (Our Story pattern) --}}
<section class="studio-banner about-banner" id="studioBanner" aria-label="Studio banner">
    <div class="about-banner-frame">
        <img
            src="{{ asset('images/studio/banner.png') }}"
            alt="Gallery of visual poetry — expressions beyond words"
            class="about-banner-image studio-banner-image"
            width="1024"
            height="682"
        >
        <div class="about-banner-veil studio-banner-veil" aria-hidden="true"></div>
        <button
            type="button"
            class="about-banner-zoom"
            id="studioBannerZoom"
            aria-label="Show full banner image"
            aria-expanded="false"
            aria-controls="studioBanner"
            title="Show full image"
        >
            <i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i>
        </button>
    </div>
</section>

<div class="studio-banner-divider" aria-hidden="true">
    <span class="studio-banner-divider-mark"></span>
</div>
