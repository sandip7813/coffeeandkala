<section class="features-edition" aria-label="Features edition">
    <div class="features-sheet">
        @if (! empty($highlighted))
            <section class="features-lead-slider features-reveal features-reveal--up" aria-label="Highlighted" data-journal-feature>
                <div class="features-kicker">
                    <span class="features-kicker-badge">Highlighted</span>
                </div>

                <div class="journal-feature-slider features-lead-feature-slider">
                    <div class="journal-feature-viewport" data-journal-feature-viewport>
                        <div class="journal-feature-container">
                            @foreach ($highlighted as $entry)
                                <article class="journal-feature-slide features-lead-slide">
                                    <div class="features-kicker">
                                        <a href="{{ route('features.show', $entry['category_id']) }}">{{ $entry['category_name'] }}</a>
                                        <time datetime="{{ $entry['date'] }}">{{ $entry['date_label'] }}</time>
                                    </div>
                                    <h3 class="features-lead-headline">
                                        <a href="{{ $entry['href'] }}">{{ $entry['title'] }}</a>
                                    </h3>

                                    <div class="features-lead-grid">
                                        <figure class="features-lead-figure">
                                            <img
                                                src="{{ $entry['thumbnail'] }}"
                                                alt=""
                                                loading="lazy"
                                                width="1200"
                                                height="720"
                                                decoding="async"
                                            >
                                            <figcaption>{{ $entry['title'] }}</figcaption>
                                        </figure>

                                        <div class="features-lead-copy">
                                            <p class="features-dropcap features-dropcap--full">{{ Str::limit($entry['excerpt'], 450) }}</p>
                                            <a href="{{ $entry['href'] }}" class="features-continued">
                                                Read the full chapter
                                                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    @if (count($highlighted) > 1)
                        <button type="button" class="journal-feature-nav journal-feature-nav--prev" data-journal-feature-prev aria-label="Previous">
                            <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="journal-feature-nav journal-feature-nav--next" data-journal-feature-next aria-label="Next">
                            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>
            </section>

            <div class="features-rule features-rule--thick" aria-hidden="true"></div>
        @endif

        @if (!empty($categories))
            <nav class="features-category-table" aria-label="Feature categories">
                @foreach ($categories as $category)
                    <a href="{{ route('features.show', $category['id']) }}" class="features-category-table-link">
                        {{ $category['name'] }}
                    </a>
                @endforeach
            </nav>
        @endif

        @if (! empty($edition))
            <section class="section-home-carousel features-carousel" aria-label="More from this edition">
                <div class="home-carousel-header">
                    <div class="home-carousel-heading">
                        <h2 class="home-carousel-title">More from this edition</h2>
                    </div>
                </div>

                <div class="home-embla home-embla--features" data-home-carousel data-slides-visible="3">
                    <div class="home-embla__viewport">
                        <div class="home-embla__container">
                            @foreach ($edition as $entry)
                                <article class="home-embla__slide">
                                    <a href="{{ $entry['href'] }}" class="home-studio-card">
                                        <div class="home-studio-card-frame">
                                            <img src="{{ $entry['image'] }}" alt="" loading="lazy" class="features-carousel-media">
                                        </div>
                                        <div class="home-studio-card-body">
                                            <h3 class="home-studio-card-title">{{ $entry['title'] }}</h3>
                                            <span class="home-studio-card-medium">{{ $entry['category_name'] }}</span>
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </div>
                    @if (count($edition) > 1)
                        <button type="button" class="home-embla-arrow home-embla-arrow--prev" data-home-prev aria-label="Previous">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="home-embla-arrow home-embla-arrow--next" data-home-next aria-label="Next">
                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>
            </section>

            <div class="features-rule features-rule--thick" aria-hidden="true"></div>
        @endif

        {{-- The most recently updated Feature articles — always automatic,
             no admin picking involved, and shown full-width now that the
             old right-hand sidebar (columns/briefs/spotlight) is gone. --}}
        @if (! empty($recentFeatures))
            <div class="features-front features-front--full">
                <div class="features-columns features-columns--full">
                    @foreach ($recentFeatures as $entry)
                        <article
                            class="features-story-card features-reveal features-reveal--up"
                            style="--portal-delay: {{ $loop->index * 0.08 }}s"
                        >
                            <div class="features-kicker">
                                <a href="{{ route('features.show', $entry['category_id']) }}">{{ $entry['category_name'] }}</a>
                                <time datetime="{{ $entry['date'] }}">{{ $entry['date_label'] }}</time>
                            </div>

                            <a href="{{ $entry['href'] }}" class="features-story-card-media" tabindex="-1" aria-hidden="true">
                                <img
                                    src="{{ $entry['image'] }}"
                                    alt=""
                                    loading="lazy"
                                    width="800"
                                    height="520"
                                    decoding="async"
                                >
                            </a>

                            <h3 class="features-story-card-headline">
                                <a href="{{ $entry['href'] }}">{{ $entry['title'] }}</a>
                            </h3>
                            <p class="features-story-card-excerpt">{{ Str::limit($entry['excerpt'], 80) }}</p>
                            <a href="{{ $entry['href'] }}" class="features-continued">Read the dispatch</a>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
