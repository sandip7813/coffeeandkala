{{--
    Shared error panel.
    Expects: $code, $eyebrow, $heading, $copy
--}}
<section class="error-page">
    <div class="error-page-inner error-reveal">
        <p class="error-code">{{ $code }}</p>
        <span class="about-ornament about-ornament--light" aria-hidden="true"></span>
        <p class="error-eyebrow">{{ $eyebrow }}</p>
        <h1 class="error-heading">{{ $heading }}</h1>
        <p class="error-copy">{{ $copy }}</p>
        <div class="error-actions">
            <a href="{{ route('home') }}" class="error-cta">
                Back to the front page
                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
            <a href="{{ route('journal') }}" class="error-cta error-cta--secondary">
                Read the journal
            </a>
        </div>
    </div>
</section>
