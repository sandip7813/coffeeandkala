{{-- Banner sits in document flow directly under the top nav --}}
<section class="journal-banner about-banner" id="journalBanner" aria-label="Journal banner">
    <div class="about-banner-frame">
        <img
            src="{{ asset('images/journal/banner.jpg') }}"
            alt="Journal — thoughts, experiences, stories that stay. Travel diaries, destination guides, local stories, and life on the road."
            class="about-banner-image journal-banner-image"
            width="1024"
            height="576"
        >
        <div class="about-banner-veil journal-banner-veil" aria-hidden="true"></div>
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

<div class="journal-banner-divider" aria-hidden="true">
    <span class="journal-banner-divider-stroke"></span>
</div>
