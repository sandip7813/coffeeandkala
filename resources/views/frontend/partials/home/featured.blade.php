{{-- Featured stories banner slider — beneath Thought of the day. Picked by a
     super admin in Admin > Home Page Sections (see App\Support\HomeSections);
     hidden entirely when nothing has been picked. --}}
@if (! empty($stories))
    @php $storyTotal = count($stories); @endphp

    <section
        id="sec-03"
        class="section-hero hero-banner hero-banner--opacity"
        aria-label="Latest Pieces"
        data-hero-banner
    >
        <div class="hero-banner-header">
            <h2 class="hero-banner-heading">Latest Pieces</h2>
        </div>

        <div class="hero-embla-wrap">
            <div class="hero-embla">
                <div class="hero-embla__viewport" data-hero-viewport>
                    <div class="hero-embla__container">
                        @foreach ($stories as $index => $story)
                            <article class="hero-embla__slide @if ($index === 0) is-active @endif">
                                <div class="hero-banner-card">
                                    <img src="{{ $story['image'] }}" alt="" loading="lazy" class="hero-banner-media">
                                    <div class="hero-banner-veil" aria-hidden="true"></div>
                                    <div class="hero-banner-content">
                                        <span class="section-num-tag hero-banner-num">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}/{{ str_pad((string) $storyTotal, 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="section-tag">{{ Str::upper($story['tag']) }}</span>
                                        <h2 class="hero-title">{{ $story['title'] }}</h2>
                                        <p class="hero-desc">{{ Str::limit($story['excerpt'], 160) }}</p>
                                        <a href="{{ $story['href'] }}" class="cta-link cta-dark">READ STORY <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>

            @if ($storyTotal > 1)
                <div class="hero-banner-controls">
                    <button type="button" class="hero-banner-arrow hero-banner-prev" aria-label="Previous story" data-hero-prev>
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    </button>

                    <div class="hero-banner-thumbs" role="tablist" aria-label="Story thumbnails">
                        @foreach ($stories as $index => $story)
                            <button
                                type="button"
                                class="hero-banner-thumb @if ($index === 0) is-active @endif"
                                role="tab"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                aria-label="Show story {{ $index + 1 }}: {{ $story['title'] }}"
                                data-hero-thumb="{{ $index }}"
                            >
                                <img
                                    src="{{ $story['thumbnail'] }}"
                                    alt="{{ $story['title'] }}"
                                    class="hero-banner-thumb-image"
                                    width="96"
                                    height="64"
                                    loading="lazy"
                                >
                            </button>
                        @endforeach
                    </div>

                    <button type="button" class="hero-banner-arrow hero-banner-next" aria-label="Next story" data-hero-next>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            @endif
        </div>

        @if ($storyTotal > 1)
            <div class="hero-banner-progress" aria-hidden="true">
                <span class="hero-banner-progress-bar" data-hero-progress></span>
            </div>
        @endif
    </section>
@endif
