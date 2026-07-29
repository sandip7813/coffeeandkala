{{-- SECTION 06: JOURNAL — Post carousel --}}
@php
    $journalPosts = [
        [
            'tag' => 'ESSAY',
            'title' => 'A Note from a Rainy Evening',
            'excerpt' => 'Raindrops, old songs and a notebook. The perfect recipe for clarity.',
            'date' => '12 Mar 2026',
            'image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=900',
        ],
        [
            'tag' => 'TRAVEL',
            'title' => 'Letters from a Slow Train',
            'excerpt' => 'Windows blur into watercolour. Somewhere between stations, a story finds its pace.',
            'date' => '28 Feb 2026',
            'image' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=900',
        ],
        [
            'tag' => 'CULTURE',
            'title' => 'The Colour of Quiet Markets',
            'excerpt' => 'Spice, cloth, and conversation — a living collage of mornings that refuse haste.',
            'date' => '14 Feb 2026',
            'image' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=900',
        ],
        [
            'tag' => 'LIFESTYLE',
            'title' => 'Brewing Between Pages',
            'excerpt' => 'Steam rising over unfinished sentences. The day begins before the world asks for anything.',
            'date' => '02 Feb 2026',
            'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=900',
        ],
        [
            'tag' => 'PHOTOGRAPHY',
            'title' => 'Holding Soft Light',
            'excerpt' => 'A frame that waits. The kind of silence that makes room for wondering.',
            'date' => '21 Jan 2026',
            'image' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=900',
        ],
        [
            'tag' => 'NOTES',
            'title' => 'Midnight Margins',
            'excerpt' => 'Ink still drying. Thoughts that only arrive when the house has gone quiet.',
            'date' => '09 Jan 2026',
            'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=900',
        ],
    ];
@endphp

<section id="sec-06" class="section-home-carousel section-journal-carousel" aria-label="From the Journal">
    <div class="home-carousel-header animate-on-scroll animate-on-scroll--text">
        <div class="home-carousel-heading">
            <h2 class="home-carousel-title">From the Journal</h2>
        </div>
    </div>

    <div class="home-embla home-embla--journal" data-home-carousel data-slides-visible="3">
        <div class="home-embla__viewport">
            <div class="home-embla__container">
                @foreach ($journalPosts as $post)
                    <article class="home-embla__slide">
                        <a href="{{ route('journal') }}" class="home-journal-card">
                            <div class="home-journal-card-media" style="background-image: url('{{ $post['image'] }}')"></div>
                            <div class="home-journal-card-body">
                                <div class="home-journal-card-meta">
                                    <span class="section-tag">{{ $post['tag'] }}</span>
                                    <time datetime="{{ $post['date'] }}">{{ $post['date'] }}</time>
                                </div>
                                <h3 class="home-journal-card-title">{{ $post['title'] }}</h3>
                                <p class="home-journal-card-excerpt">{{ $post['excerpt'] }}</p>
                                <span class="cta-link cta-light">READ NOTE <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></span>
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
