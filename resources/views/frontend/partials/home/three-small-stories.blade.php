{{-- SECTION 04: The Selection — picked by a super admin in Admin > Home
     Page Sections (see App\Support\HomeSections); hidden entirely when
     nothing has been picked. --}}
@if (! empty($stories))
    <section
        id="sec-04"
        class="section-three-stories"
        aria-label="The Selection"
    >
        <header class="three-stories-header">
            <div class="three-stories-heading">
                <h2 class="three-stories-title">The Selection</h2>
            </div>

            <a href="{{ route('features') }}" class="three-stories-cta" aria-label="Unfold the stories">
                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
        </header>

        <div class="three-stories-grid">
            @foreach ($stories as $story)
                <article class="three-story">
                    <a href="{{ $story['href'] }}" class="three-story-card">
                        <img src="{{ $story['image'] }}" alt="{{ $story['title'] }}" loading="lazy" class="three-story-media">

                        <div class="three-story-body">
                            <span class="three-story-tag">{{ Str::upper($story['tag']) }}</span>
                            <h3 class="three-story-heading">{{ $story['title'] }}</h3>
                            <span class="three-story-link">
                                Read Story
                                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                            </span>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </section>
@endif
