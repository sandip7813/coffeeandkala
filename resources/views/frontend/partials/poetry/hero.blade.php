{{-- HERO — a hallway of open doors. The banner is a finished composite (title
     and tagline are baked into the image), so the markup only frames it. --}}
<section class="poetry-banner about-banner" id="poetryBanner" aria-label="Poetry banner">
    <div class="about-banner-frame">
        <img
            src="{{ asset('images/poetry/hero.png') }}"
            alt="A hallway of sunlit archways, one opening into another — Poetry. Words that breathe. Emotions that linger. Stories that stay."
            class="about-banner-image poetry-banner-image"
            width="1774"
            height="887"
        >
        <div class="about-banner-veil poetry-banner-veil" aria-hidden="true"></div>

        <button
            type="button"
            class="about-banner-zoom"
            id="poetryBannerZoom"
            aria-label="Show full banner image"
            aria-expanded="false"
            aria-controls="poetryBanner"
            title="Show full image"
        >
            <i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i>
        </button>
    </div>
</section>

<div class="poetry-banner-divider" aria-hidden="true">
    <span class="poetry-banner-divider-mark"></span>
</div>
