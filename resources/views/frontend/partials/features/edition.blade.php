<section class="features-edition" aria-label="Features edition">
    <div class="features-sheet">
        @php
            $lead = collect($entries)->firstWhere('role', 'lead');
            $features = collect($entries)->where('role', 'feature')->values();
            $columns = collect($entries)->where('role', 'column')->values();
            $briefs = collect($entries)->where('role', 'brief')->values();
            $spotlight = collect($entries)->firstWhere('role', 'spotlight');
        @endphp

        @if ($lead)
            <article class="features-lead features-reveal features-reveal--up">
                <div class="features-kicker">
                    <span class="features-kicker-badge">Article of the day</span>
                    <time datetime="{{ $lead['date'] }}">{{ $lead['date_label'] }}</time>
                </div>
                <h3 class="features-lead-headline">
                    <a href="{{ $lead['href'] }}">{{ $lead['title'] }}</a>
                </h3>
                <p class="features-byline">
                    <a href="{{ route('features.show', $lead['category_id']) }}">{{ $lead['category_name'] }}</a>
                    · {{ $lead['tag'] }}
                </p>

                <div class="features-lead-grid">
                    <figure class="features-lead-figure">
                        <img
                            src="{{ $lead['image'] }}"
                            alt=""
                            loading="lazy"
                            width="1200"
                            height="720"
                            decoding="async"
                        >
                        <figcaption>{{ $lead['title'] }}</figcaption>
                    </figure>

                    <div class="features-lead-copy">
                        <p class="features-dropcap">{{ $lead['excerpt'] }}</p>
                        <a href="{{ $lead['href'] }}" class="features-continued">
                            Read the full chapter
                            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </article>
        @endif

        <div class="features-rule features-rule--thick" aria-hidden="true"></div>

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
                        <p class="features-story-card-excerpt">{{ $entry['excerpt'] }}</p>
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
                        <p>{{ $entry['excerpt'] }}</p>
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
                                        <span>{{ $entry['excerpt'] }}</span>
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
                        <p class="features-spotlight-excerpt">{{ $spotlight['excerpt'] }}</p>
                        <a href="{{ $spotlight['href'] }}" class="features-continued">Read the dispatch</a>
                    </article>
                @endif
            </aside>
        </div>
    </div>
</section>
