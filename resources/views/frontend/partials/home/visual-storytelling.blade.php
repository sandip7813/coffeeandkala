{{-- SECTION 03: VISUAL STORYTELLING — Gallery Embla (6 items, 4 visible) --}}
@php
    $gallerySlides = [
        [
            'title' => 'The Last Light',
            'place' => 'Northern Rail Corridor',
            'image' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=900',
        ],
        [
            'title' => 'Crimson Passage',
            'place' => 'Himalayan Foothills',
            'image' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?q=80&w=900',
        ],
        [
            'title' => 'Lone Oar',
            'place' => 'Backwaters at First Light',
            'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?q=80&w=900',
        ],
        [
            'title' => 'Weathered Light',
            'place' => 'Portrait Study',
            'image' => 'https://images.unsplash.com/photo-1566616213894-2d4e1baee5d8?q=80&w=900',
        ],
        [
            'title' => 'Morning Ritual',
            'place' => 'Studio Kitchen',
            'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=900',
        ],
        [
            'title' => 'Market Breath',
            'place' => 'Bazaar Lane',
            'image' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=900',
        ],
    ];
@endphp

<section id="sec-03" class="section-home-carousel section-visual-storytelling" aria-label="Visual Storytelling">
    <div class="home-carousel-header animate-on-scroll animate-on-scroll--text">
        <div class="home-carousel-heading">
            <h2 class="home-carousel-title">Visual Storytelling</h2>
        </div>
        <a href="{{ route('gallery') }}" class="home-carousel-link">Explore Gallery <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
    </div>

    <div class="home-embla home-embla--gallery" data-home-carousel data-slides-visible="4">
        <div class="home-embla__viewport">
            <div class="home-embla__container">
                @foreach ($gallerySlides as $slide)
                    <article class="home-embla__slide">
                        <a href="{{ route('gallery') }}" class="home-gallery-card">
                            <div class="home-gallery-card-media" style="background-image: url('{{ $slide['image'] }}')"></div>
                            <div class="home-gallery-card-body">
                                <h3 class="home-gallery-card-title">{{ $slide['title'] }}</h3>
                                <span class="home-gallery-card-place">{{ $slide['place'] }}</span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
        <button type="button" class="home-embla-arrow home-embla-arrow--prev" data-home-prev aria-label="Previous">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
        </button>
        <button type="button" class="home-embla-arrow home-embla-arrow--next" data-home-next aria-label="Next">
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </button>
    </div>
</section>
