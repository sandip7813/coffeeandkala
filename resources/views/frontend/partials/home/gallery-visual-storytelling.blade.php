{{-- SECTION 06: Gallery of Visual Storytelling — picked by a super admin
     from the Gallery admin list/edit page (see App\Support\HomeMediaSections);
     hidden entirely when nothing has been picked. --}}
@if (! empty($slides))
    <section
        id="sec-06"
        class="section-gallery-story"
        aria-label="Gallery of Visual Storytelling"
        data-gallery-story
    >
        <header class="gallery-story-header">
            <div class="gallery-story-heading">
                <h2 class="gallery-story-title">Gallery</h2>
            </div>
            <a href="{{ route('gallery') }}" class="gallery-story-view-all" aria-label="Wander the exhibition">
                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
        </header>

        <div class="gallery-story-body">
            <ol class="gallery-story-index" aria-label="Slide markers">
                @foreach ($slides as $index => $slide)
                    <li>
                        <button
                            type="button"
                            class="gallery-story-index-btn @if ($index === 0) is-active @endif"
                            data-gallery-story-index="{{ $index }}"
                            aria-label="Go to slide {{ $index + 1 }}"
                        >{{ $index + 1 }}</button>
                    </li>
                @endforeach
            </ol>

            <div class="gallery-story-slider">
                <div class="gallery-story-viewport" data-gallery-story-viewport>
                    <div class="gallery-story-container">
                        @foreach ($slides as $slide)
                            <article class="gallery-story-slide">
                                <a href="{{ route('gallery') }}" class="gallery-story-card">
                                    <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" loading="lazy" class="gallery-story-media">
                                    <div class="gallery-story-caption">
                                        <h3 class="gallery-story-card-title">{{ $slide['title'] }}</h3>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>

                @if (count($slides) > 1)
                    <button type="button" class="gallery-story-nav gallery-story-nav--prev" data-gallery-story-prev aria-label="Previous">
                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="gallery-story-nav gallery-story-nav--next" data-gallery-story-next aria-label="Next">
                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                    </button>
                @endif
            </div>
        </div>
    </section>
@endif
