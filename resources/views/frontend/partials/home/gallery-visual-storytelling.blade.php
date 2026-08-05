{{-- SECTION 06: Gallery of Visual Storytelling --}}
@php
    $galleryStorySlides = [
        [
            'place' => 'Jodhpur, India',
            'year' => '2022',
            'title' => 'Blue City Mornings',
            'image' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=1200',
        ],
        [
            'place' => 'Himalayan Foothills',
            'year' => '2023',
            'title' => 'Quiet Pines',
            'image' => 'https://images.unsplash.com/photo-1511497584788-876760111969?q=80&w=1200',
        ],
        [
            'place' => 'Rann of Kutch',
            'year' => '2021',
            'title' => 'Wing & Light',
            'image' => 'https://images.unsplash.com/photo-1444464666168-49d633b86797?q=80&w=1200',
        ],
        [
            'place' => 'Kerala Backwaters',
            'year' => '2024',
            'title' => 'Lone Oar',
            'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?q=80&w=1200',
        ],
        [
            'place' => 'Northern Rail Corridor',
            'year' => '2022',
            'title' => 'The Last Light',
            'image' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=1200',
        ],
        [
            'place' => 'Studio Kitchen',
            'year' => '2023',
            'title' => 'Morning Ritual',
            'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1200',
        ],
    ];
    $galleryStoryRomans = ['I', 'II', 'III', 'IV'];
@endphp

<section
    id="sec-06"
    class="section-gallery-story"
    aria-label="Gallery of Visual Storytelling"
    data-gallery-story
>
    <header class="gallery-story-header">
        <div class="gallery-story-heading">
            <p class="gallery-story-eyebrow">Gallery</p>
            <h2 class="gallery-story-title">Gallery of Visual Storytelling</h2>
        </div>
        <a href="{{ route('gallery') }}" class="gallery-story-view-all">
            Wander the Exhibition
            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
        </a>
    </header>

    <div class="gallery-story-body">
        <ol class="gallery-story-index" aria-label="Slide markers">
            @foreach ($galleryStoryRomans as $index => $roman)
                <li>
                    <button
                        type="button"
                        class="gallery-story-index-btn @if ($index === 0) is-active @endif"
                        data-gallery-story-index="{{ $index }}"
                        aria-label="Go to slide {{ $index + 1 }}"
                    >{{ $roman }}</button>
                </li>
            @endforeach
        </ol>

        <div class="gallery-story-slider">
            <div class="gallery-story-viewport" data-gallery-story-viewport>
                <div class="gallery-story-container">
                    @foreach ($galleryStorySlides as $slide)
                        <article class="gallery-story-slide">
                            <a href="{{ route('gallery') }}" class="gallery-story-card">
                                <div
                                    class="gallery-story-media"
                                    style="background-image: url('{{ $slide['image'] }}')"
                                ></div>
                                <div class="gallery-story-caption">
                                    <span class="gallery-story-meta">{{ $slide['place'] }} — {{ $slide['year'] }}</span>
                                    <h3 class="gallery-story-card-title">{{ $slide['title'] }}</h3>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>

            <button type="button" class="gallery-story-nav gallery-story-nav--prev" data-gallery-story-prev aria-label="Previous">
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </button>
            <button type="button" class="gallery-story-nav gallery-story-nav--next" data-gallery-story-next aria-label="Next">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</section>
