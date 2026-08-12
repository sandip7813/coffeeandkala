{{-- HERO — bold, immersive Espresso entrance. "THE JOURNAL" straddles the seam
     between the Espresso field and the full-width signature banner beneath it:
     the top half of the letters sit on Espresso, the bottom half sits on the image. --}}
<section class="journal-hero" id="journalHero" aria-label="The Journal">
    <h1 class="journal-hero-title">The Journal</h1>

    <div class="journal-hero-banner" id="journalBanner">
        <img
            src="{{ asset('images/journal/journal-hero.png') }}"
            alt="A reader at a wooden table holds open a copy of The Journal, lit warmly against a dark garden."
            class="journal-hero-image"
            width="815"
            height="455"
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
    </div>
</section>
