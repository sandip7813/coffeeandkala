{{-- SECTION 07: Journal — picked by a super admin in Admin > Home Page
     Sections (see App\Support\HomeSections); hidden entirely when nothing
     has been picked. --}}
@if (! empty($posts))
    <section
        id="sec-07"
        class="section-journal-feature"
        aria-label="Journal"
        data-journal-feature
    >
        <header class="journal-feature-header">
            <div class="journal-feature-heading">
                <h2 class="journal-feature-title">Journal</h2>
            </div>
            <a href="{{ route('journal') }}" class="journal-feature-view-all" aria-label="Read the journal">
                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
        </header>

        <div class="journal-feature-slider">
            <div class="journal-feature-viewport" data-journal-feature-viewport>
                <div class="journal-feature-container">
                    @foreach ($posts as $post)
                        <article class="journal-feature-slide">
                            <div class="journal-feature-rule journal-feature-rule--top" aria-hidden="true"></div>

                            <div class="journal-feature-block">
                                <div class="journal-feature-media">
                                    <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" loading="lazy" class="journal-feature-media-img">
                                </div>

                                <div class="journal-feature-panel">
                                    <p class="journal-feature-meta">
                                        {{ Str::upper($post['category_name']) }}
                                    </p>
                                    <h3 class="journal-feature-card-title">
                                        <a href="{{ $post['href'] }}">{{ $post['title'] }}</a>
                                    </h3>
                                    <p class="journal-feature-excerpt">{{ Str::limit($post['excerpt'], 220) }}</p>
                                    <time class="journal-feature-date" datetime="{{ $post['date'] }}">{{ $post['date_label'] }}</time>
                                </div>
                            </div>

                            <div class="journal-feature-rule journal-feature-rule--bottom" aria-hidden="true"></div>
                        </article>
                    @endforeach
                </div>
            </div>

            @if (count($posts) > 1)
                <button type="button" class="journal-feature-nav journal-feature-nav--prev" data-journal-feature-prev aria-label="Previous">
                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                </button>
                <button type="button" class="journal-feature-nav journal-feature-nav--next" data-journal-feature-next aria-label="Next">
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                </button>
            @endif
        </div>
    </section>
@endif
