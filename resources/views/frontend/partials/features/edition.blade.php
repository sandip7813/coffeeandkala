<section class="features-edition" aria-label="Features edition">
    <div class="features-sheet">
        @php
            $lead = collect($entries)->firstWhere('role', 'lead');
            $features = collect($entries)->where('role', 'feature')->values();
            $columns = collect($entries)->where('role', 'column')->values();
            $briefs = collect($entries)->where('role', 'brief')->values();
            $spotlight = collect($entries)->firstWhere('role', 'spotlight');
        @endphp

        @php
            $leadSlides = collect([$lead])->merge($features)->filter()->values();
        @endphp

        @if ($leadSlides->isNotEmpty())
            <section class="features-lead-slider features-reveal features-reveal--up" aria-label="Article of the day" data-journal-feature>
                <div class="features-kicker">
                    <span class="features-kicker-badge">Article of the day</span>
                </div>

                <div class="journal-feature-slider features-lead-feature-slider">
                    <div class="journal-feature-viewport" data-journal-feature-viewport>
                        <div class="journal-feature-container">
                            @foreach ($leadSlides as $entry)
                                <article class="journal-feature-slide features-lead-slide">
                                    <time datetime="{{ $entry['date'] }}" class="features-lead-slide-date">{{ $entry['date_label'] }}</time>
                                    <h3 class="features-lead-headline">
                                        <a href="{{ $entry['href'] }}">{{ $entry['title'] }}</a>
                                    </h3>
                                    <p class="features-byline">
                                        <a href="{{ route('features.show', $entry['category_id']) }}">{{ $entry['category_name'] }}</a>
                                        · {{ $entry['tag'] }}
                                    </p>

                                    <div class="features-lead-grid">
                                        <figure class="features-lead-figure">
                                            <img
                                                src="{{ $entry['image'] }}"
                                                alt=""
                                                loading="lazy"
                                                width="1200"
                                                height="720"
                                                decoding="async"
                                            >
                                            <figcaption>{{ $entry['title'] }}</figcaption>
                                        </figure>

                                        <div class="features-lead-copy">
                                            <p class="features-dropcap features-dropcap--full">{{ $entry['excerpt'] }}</p>
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

                    <button type="button" class="journal-feature-nav journal-feature-nav--prev" data-journal-feature-prev aria-label="Previous">
                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="journal-feature-nav journal-feature-nav--next" data-journal-feature-next aria-label="Next">
                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                    </button>
                </div>
            </section>
        @endif

        <div class="features-rule features-rule--thick" aria-hidden="true"></div>

        @php
            $carouselEntries = $columns->merge($briefs)->merge($features)->values();
        @endphp

        @if ($carouselEntries->isNotEmpty())
            <section class="section-home-carousel features-carousel" aria-label="More from this edition">
                @if (!empty($categories))
                    <nav class="features-category-table" aria-label="Feature categories">
                        @foreach ($categories as $category)
                            <a href="{{ route('features.show', $category['id']) }}" class="features-category-table-link">
                                {{ $category['name'] }}
                            </a>
                        @endforeach
                    </nav>
                @endif

                <div class="home-carousel-header">
                    <div class="home-carousel-heading">
                        <h2 class="home-carousel-title">More from this edition</h2>
                    </div>
                </div>

                <div class="home-embla home-embla--features" data-home-carousel data-slides-visible="3">
                    <div class="home-embla__viewport">
                        <div class="home-embla__container">
                            @foreach ($carouselEntries as $entry)
                                <article class="home-embla__slide">
                                    <a href="{{ $entry['href'] }}" class="home-studio-card">
                                        <div class="home-studio-card-frame">
                                            <div class="home-studio-card-media" style="background-image: url('{{ $entry['image'] }}')"></div>
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
                    <button type="button" class="home-embla-arrow home-embla-arrow--prev" data-home-prev aria-label="Previous">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="home-embla-arrow home-embla-arrow--next" data-home-next aria-label="Next">
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </section>

            <div class="features-rule features-rule--thick" aria-hidden="true"></div>
        @endif

        <div class="features-front">
            <div class="features-columns">
                @foreach ($features as $entry)
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

            <aside class="features-sidebar features-reveal features-reveal--up" aria-label="Column &amp; briefs">
                @foreach ($columns as $entry)
                    <article class="features-column-piece">
                        <div class="features-kicker">
                            <a href="{{ route('features.show', $entry['category_id']) }}">{{ $entry['category_name'] }}</a>
                            <time datetime="{{ $entry['date'] }}">{{ $entry['date_label'] }}</time>
                        </div>
                        <a href="{{ $entry['href'] }}" class="features-column-media" tabindex="-1" aria-hidden="true">
                            <img
                                src="{{ $entry['image'] }}"
                                alt=""
                                loading="lazy"
                                width="640"
                                height="400"
                                decoding="async"
                            >
                        </a>
                        <h3>
                            <a href="{{ $entry['href'] }}">{{ $entry['title'] }}</a>
                        </h3>
                        <p>{{ Str::limit($entry['excerpt'], 80) }}</p>
                    </article>
                @endforeach

                <div class="features-briefs">
                    <h3 class="features-briefs-title">In brief</h3>
                    <ul>
                        @foreach ($briefs as $entry)
                            <li>
                                <a href="{{ $entry['href'] }}" class="features-brief-link">
                                    <span class="features-brief-media">
                                        <img
                                            src="{{ $entry['image'] }}"
                                            alt=""
                                            loading="lazy"
                                            width="240"
                                            height="180"
                                            decoding="async"
                                        >
                                    </span>
                                    <span class="features-brief-copy">
                                        <strong>{{ $entry['title'] }}</strong>
                                        <span>{{ Str::limit($entry['excerpt'], 80) }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if ($spotlight)
                    <article class="features-spotlight">
                        <div class="features-kicker">
                            <a href="{{ route('features.show', $spotlight['category_id']) }}">{{ $spotlight['category_name'] }}</a>
                            <time datetime="{{ $spotlight['date'] }}">{{ $spotlight['date_label'] }}</time>
                        </div>
                        <h3 class="features-spotlight-headline">
                            <a href="{{ $spotlight['href'] }}">{{ $spotlight['title'] }}</a>
                        </h3>
                        <p class="features-spotlight-excerpt">{{ Str::limit($spotlight['excerpt'], 80) }}</p>
                        <a href="{{ $spotlight['href'] }}" class="features-continued">Read the dispatch</a>
                    </article>
                @endif
            </aside>
        </div>
    </div>
</section>
