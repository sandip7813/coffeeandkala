{{-- SECTION 01: HERO — Slow Publishing editorial intro --}}
<section
    id="sec-01"
    class="section-hero hero-editorial"
    aria-label="Coffee & Kala — the home of slow publishing"
    data-hero-editorial
>
    <div
        class="hero-editorial-media"
        style="background-image: url('{{ asset('images/home/hero-slow-publishing.jpg') }}')"
        role="img"
        aria-label="A latte with leaf art beside a brass service bell and receipt spindle on a wooden cafe counter"
    ></div>
    <div class="hero-editorial-veil" aria-hidden="true"></div>

    <div class="hero-editorial-content">
        <p class="hero-editorial-pillars hero-reveal" data-hero-reveal data-reveal-mode="words" data-reveal-delay="280">
            ART. CULTURE. TRAVEL. LIFESTYLE. PEOPLE. PERCEPTION
        </p>

        <h1 class="hero-editorial-title">
            <span class="hero-editorial-title-lead hero-reveal" data-hero-reveal data-reveal-mode="words" data-reveal-delay="900">
                The Home Of
            </span>
            <span class="hero-editorial-title-script hero-reveal" data-hero-reveal data-reveal-mode="words" data-reveal-delay="1600">
                Slow Publishing
            </span>
        </h1>

        <p class="hero-editorial-tagline hero-reveal" data-hero-reveal data-reveal-mode="words" data-reveal-delay="2400">
            An independent editorial magazine where every piece is crafted with time, guided by intention and published with purpose
        </p>
    </div>

    <button type="button" class="hero-scroll" data-hero-scroll-to="sec-02" aria-label="Scroll to thought of the day">
        <span class="hero-scroll-label">SCROLL</span>
        <span class="hero-scroll-arrow" aria-hidden="true">
            <svg class="hero-scroll-icon" viewBox="0 0 28 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="8" y="2" width="12" height="22" rx="6" stroke="currentColor" stroke-width="1.4"/>
                <circle class="hero-scroll-dot" cx="14" cy="9" r="1.6" fill="currentColor"/>
                <path d="M14 28v12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                <path d="M9.5 36.5L14 42.5L18.5 36.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
    </button>
</section>
