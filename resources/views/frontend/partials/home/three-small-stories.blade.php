{{-- SECTION 04: Three Small Stories --}}
@php
    $smallStories = [
        [
            'number' => '01',
            'tag' => 'PLACE',
            'title' => 'The Window Seat in Udaipur',
            'excerpt' => 'A morning that refused the itinerary — lake light, slow steam, and a city that lets you arrive twice.',
            'image' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=900',
        ],
        [
            'number' => '02',
            'tag' => 'RITUAL',
            'title' => 'The Second Cup',
            'excerpt' => 'Between the first pour and the open page — a quiet interval where the day decides its pace.',
            'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=900',
        ],
        [
            'number' => '03',
            'tag' => 'PEOPLE',
            'title' => 'Tableside Echoes',
            'excerpt' => 'Names half-remembered, laughter folded into the evening — stories that stay after the cups are cleared.',
            'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=900',
        ],
    ];
@endphp

<section
    id="sec-04"
    class="section-three-stories"
    aria-label="Three Small Stories"
>
    <header class="three-stories-header">
        <div class="three-stories-heading">
            <p class="three-stories-eyebrow">From the table</p>
            <h2 class="three-stories-title">Three Small Stories</h2>
        </div>

        <a href="{{ route('features') }}" class="three-stories-cta">
            Unfold the Stories
            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
        </a>
    </header>

    <div class="three-stories-grid">
        @foreach ($smallStories as $story)
            <article class="three-story">
                <a href="{{ route('features') }}" class="three-story-card">
                    <div
                        class="three-story-media"
                        style="background-image: url('{{ $story['image'] }}')"
                        role="img"
                        aria-label="{{ $story['title'] }}"
                    ></div>

                    <span class="three-story-corner three-story-corner--tl" aria-hidden="true"></span>
                    <span class="three-story-corner three-story-corner--tr" aria-hidden="true"></span>
                    <span class="three-story-corner three-story-corner--bl" aria-hidden="true"></span>
                    <span class="three-story-corner three-story-corner--br" aria-hidden="true"></span>

                    <span class="three-story-number">{{ $story['number'] }}</span>

                    <div class="three-story-overlay">
                        <span class="three-story-tag">{{ $story['tag'] }}</span>
                        <h3 class="three-story-heading">{{ $story['title'] }}</h3>
                        <p class="three-story-excerpt">{{ $story['excerpt'] }}</p>
                        <span class="three-story-link">
                            Read story
                            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                        </span>
                    </div>
                </a>
            </article>
        @endforeach
    </div>
</section>
