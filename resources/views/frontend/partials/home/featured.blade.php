{{-- Featured stories banner slider — beneath Thought of the day --}}
@php
    $heroStories = [
        [
            'tag' => 'TRAVEL',
            'title' => 'In the heart of Jaipur',
            'desc' => 'A slow morning in the Pink City — where every corner carries a story older than time.',
            'image' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=1800',
            'thumb' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=320',
        ],
        [
            'tag' => 'LIFESTYLE',
            'title' => 'Brewed at dawn',
            'desc' => 'Quiet cups, open notebooks, and the soft ritual that starts every creative day.',
            'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1800',
            'thumb' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=320',
        ],
        [
            'tag' => 'PHOTOGRAPHY',
            'title' => 'Light over landscape',
            'desc' => 'Frames that linger — where stillness, color, and memory meet in one glance.',
            'image' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=1800',
            'thumb' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=320',
        ],
        [
            'tag' => 'ESSAYS',
            'title' => 'Words between sips',
            'desc' => 'Short reflections for long evenings — stories written the way coffee is poured: slowly.',
            'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=1800',
            'thumb' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=320',
        ],
    ];
    $heroTotal = count($heroStories);
@endphp

<section
    id="sec-03"
    class="section-hero hero-banner hero-banner--opacity"
    aria-label="Featured stories"
    data-hero-banner
>
    <div class="hero-banner-header">
        <h2 class="hero-banner-heading">Latest Pieces</h2>
    </div>

    <div class="hero-embla-wrap">
        <div class="hero-embla">
            <div class="hero-embla__viewport" data-hero-viewport>
                <div class="hero-embla__container">
                    @foreach ($heroStories as $index => $story)
                        <article class="hero-embla__slide @if ($index === 0) is-active @endif">
                            <div class="hero-banner-card">
                                <div
                                    class="hero-banner-media"
                                    style="background-image: url('{{ $story['image'] }}')"
                                ></div>
                                <div class="hero-banner-veil" aria-hidden="true"></div>
                                <div class="hero-banner-content">
                                    <span class="section-num-tag hero-banner-num">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}/{{ str_pad((string) $heroTotal, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="section-tag">{{ $story['tag'] }}</span>
                                    <h2 class="hero-title">{{ $story['title'] }}</h2>
                                    <p class="hero-desc">{{ $story['desc'] }}</p>
                                    <a href="#" class="cta-link cta-dark">READ STORY <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="hero-banner-controls">
            <button type="button" class="hero-banner-arrow hero-banner-prev" aria-label="Previous story" data-hero-prev>
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            </button>

            <div class="hero-banner-thumbs" role="tablist" aria-label="Story thumbnails">
                @foreach ($heroStories as $index => $story)
                    <button
                        type="button"
                        class="hero-banner-thumb @if ($index === 0) is-active @endif"
                        role="tab"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-label="Show story {{ $index + 1 }}: {{ $story['title'] }}"
                        data-hero-thumb="{{ $index }}"
                    >
                        <img
                            src="{{ $story['thumb'] }}"
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
    </div>

    <div class="hero-banner-progress" aria-hidden="true">
        <span class="hero-banner-progress-bar" data-hero-progress></span>
    </div>
</section>
