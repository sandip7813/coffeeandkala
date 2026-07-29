{{-- Banner sits in document flow directly under the top nav --}}
<section class="gallery-banner about-banner" id="galleryBanner" aria-label="Gallery banner">
    <div class="about-banner-frame">
        <img
            src="{{ asset('images/gallery/banner.png') }}"
            alt="Gallery of Visual Storytelling — every frame a moment, a story"
            class="about-banner-image gallery-banner-image"
            width="1024"
            height="682"
        >
        <div class="about-banner-veil gallery-banner-veil" aria-hidden="true"></div>
        <button
            type="button"
            class="about-banner-zoom"
            id="galleryBannerZoom"
            aria-label="Show full banner image"
            aria-expanded="false"
            aria-controls="galleryBanner"
            title="Show full image"
        >
            <i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i>
        </button>
    </div>
</section>

<div class="gallery-banner-divider" aria-hidden="true">
    <span class="gallery-banner-divider-mark"></span>
</div>
