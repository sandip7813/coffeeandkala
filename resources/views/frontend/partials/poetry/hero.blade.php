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
    </div>
</section>

<div class="poetry-banner-divider" aria-hidden="true">
    <svg class="poetry-banner-divider-ornament" width="230" height="18" viewBox="0 0 260 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 10 104 9.2v1.6L0 10ZM260 10 156 9.2v1.6L260 10Z" fill="currentColor" />
        <circle cx="108" cy="10" r="2" fill="currentColor" />
        <circle cx="152" cy="10" r="2" fill="currentColor" />
        <circle cx="117" cy="10" r="3" stroke="currentColor" stroke-width="1.4" />
        <circle cx="143" cy="10" r="3" stroke="currentColor" stroke-width="1.4" />
        <path d="M130 2q1.5 6.5 8 8-6.5 1.5-8 8-1.5-6.5-8-8 6.5-1.5 8-8Z" fill="currentColor" />
    </svg>
</div>
