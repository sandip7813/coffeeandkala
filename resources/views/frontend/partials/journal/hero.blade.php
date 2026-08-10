{{-- HERO — bold, immersive Espresso entrance. "THE JOURNAL" straddles the seam
     between the Espresso field and the full-width signature banner beneath it:
     the top half of the letters sit on Espresso, the bottom half sits on the image. --}}
<section class="journal-hero" id="journalHero" aria-label="The Journal">
    <h1 class="journal-hero-title">The Journal</h1>

    <div class="journal-hero-banner" id="journalBanner">
        <img
            src="{{ asset('images/journal/hero.png') }}"
            alt="A writing desk scene with coffee, dried flowers, handmade paper and an open notebook."
            class="journal-hero-image"
            width="1717"
            height="916"
        >
        <div class="journal-hero-veil" aria-hidden="true"></div>

        <button
            type="button"
            class="about-banner-zoom"
            id="journalBannerZoom"
            aria-label="Show full banner image"
            aria-expanded="false"
            aria-controls="journalBanner"
            title="Show full image"
        >
            <i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i>
        </button>

        <button type="button" class="hero-scroll" data-hero-scroll-to="journalIntro" aria-label="Scroll to the journal">
            <span class="hero-scroll-label">SCROLL</span>
            <span class="hero-scroll-arrow" aria-hidden="true">
                <svg class="hero-scroll-icon" viewBox="0 0 20 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <line class="hero-scroll-line" x1="10" y1="0" x2="10" y2="40" stroke="currentColor" stroke-width="1.4"/>
                    <path class="hero-scroll-head" d="M2 32L10 44L18 32" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </button>
    </div>
</section>
