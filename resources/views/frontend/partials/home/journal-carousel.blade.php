{{-- SECTION 07: FROM THE JOURNAL — editorial feature slider --}}
@php
    $journalPosts = [
        [
            'tag' => 'Essay',
            'collection' => 'Quiet Hours',
            'title' => 'A Note from a Rainy Evening',
            'excerpt' => 'Raindrops, old songs and a notebook. The perfect recipe for clarity — a slow pour of thoughts that only arrive when the house has gone quiet.',
            'date' => '12 Mar 2026',
            'image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=1600',
        ],
        [
            'tag' => 'Travel',
            'collection' => 'Slow Corridors',
            'title' => 'Letters from a Slow Train',
            'excerpt' => 'Windows blur into watercolour. Somewhere between stations, a story finds its pace — and the journey becomes the essay.',
            'date' => '28 Feb 2026',
            'image' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=1600',
        ],
        [
            'tag' => 'Culture',
            'collection' => 'Market Light',
            'title' => 'The Colour of Quiet Markets',
            'excerpt' => 'Spice, cloth, and conversation — a living collage of mornings that refuse haste, and colours that linger long after the stall is packed away.',
            'date' => '14 Feb 2026',
            'image' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=1600',
        ],
        [
            'tag' => 'Lifestyle',
            'collection' => 'Morning Ritual',
            'title' => 'Brewing Between Pages',
            'excerpt' => 'Steam rising over unfinished sentences. The day begins before the world asks for anything — cup warm, page open, mind unhurried.',
            'date' => '02 Feb 2026',
            'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1600',
        ],
    ];
@endphp

<section
    id="sec-07"
    class="section-journal-feature"
    aria-label="From the Journal"
    data-journal-feature
>
    <header class="journal-feature-header">
        <div class="journal-feature-heading">
            <p class="journal-feature-eyebrow">Blogs</p>
            <h2 class="journal-feature-title">From the Journal</h2>
        </div>
        <a href="{{ route('journal') }}" class="journal-feature-view-all">
            Read the Journal
            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
        </a>
    </header>

    <div class="journal-feature-slider">
        <div class="journal-feature-viewport" data-journal-feature-viewport>
            <div class="journal-feature-container">
                @foreach ($journalPosts as $post)
                    <article class="journal-feature-slide">
                        <div class="journal-feature-stage">
                            <div
                                class="journal-feature-media"
                                style="background-image: url('{{ $post['image'] }}')"
                                role="img"
                                aria-label="{{ $post['title'] }}"
                            >
                                <span class="journal-feature-corner journal-feature-corner--tl" aria-hidden="true"></span>
                                <span class="journal-feature-corner journal-feature-corner--tr" aria-hidden="true"></span>
                                <span class="journal-feature-corner journal-feature-corner--bl" aria-hidden="true"></span>
                                <span class="journal-feature-corner journal-feature-corner--br" aria-hidden="true"></span>
                            </div>

                            <div class="journal-feature-panel">
                                <span class="journal-feature-corner journal-feature-corner--tl" aria-hidden="true"></span>
                                <span class="journal-feature-corner journal-feature-corner--tr" aria-hidden="true"></span>
                                <span class="journal-feature-corner journal-feature-corner--bl" aria-hidden="true"></span>
                                <span class="journal-feature-corner journal-feature-corner--br" aria-hidden="true"></span>

                                <p class="journal-feature-meta">
                                    {{ strtoupper($post['collection']) }} — {{ strtoupper($post['tag']) }}
                                </p>
                                <h3 class="journal-feature-card-title">
                                    <a href="{{ route('journal') }}">{{ $post['title'] }}</a>
                                </h3>
                                <p class="journal-feature-excerpt">{{ $post['excerpt'] }}</p>
                                <time class="journal-feature-date" datetime="{{ $post['date'] }}">{{ $post['date'] }}</time>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <button type="button" class="journal-feature-nav journal-feature-nav--prev" data-journal-feature-prev aria-label="Previous">
            <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
        </button>
        <button type="button" class="journal-feature-nav journal-feature-nav--next" data-journal-feature-next aria-label="Next">
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
        </button>
    </div>
</section>
