<section class="journal-edition" aria-label="Journal edition">
    <div class="journal-sheet">
        @php
            $lead = collect($entries)->firstWhere('role', 'lead');
            $features = collect($entries)->where('role', 'feature')->values();
            $columns = collect($entries)->where('role', 'column')->values();
            $briefs = collect($entries)->where('role', 'brief')->values();
        @endphp

        @if ($lead)
            <article
                class="journal-lead journal-reveal journal-reveal--up"
                @if (! empty($lead['category_id'])) id="{{ $lead['category_id'] }}" @endif
            >
                <div class="journal-kicker">
                    <span>{{ $lead['tag'] }}</span>
                    <time datetime="{{ $lead['date'] }}">{{ $lead['date_label'] }}</time>
                </div>
                <h3 class="journal-lead-headline">
                    <a href="{{ $lead['href'] }}">{{ $lead['title'] }}</a>
                </h3>
                <p class="journal-byline">By the Coffee &amp; Kala desk</p>

                <div class="journal-lead-grid">
                    <figure class="journal-lead-figure">
                        <img
                            src="{{ $lead['image'] }}"
                            alt=""
                            loading="lazy"
                            width="1200"
                            height="720"
                            decoding="async"
                        >
                        <figcaption>{{ $lead['caption'] ?? $lead['title'] }}</figcaption>
                    </figure>

                    <div class="journal-lead-copy">
                        <p class="journal-dropcap">{{ $lead['excerpt'] }}</p>
                        <a href="{{ $lead['href'] }}" class="journal-continued">
                            Continued on page two
                            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </article>
        @endif

        <div class="journal-rule journal-rule--thick" aria-hidden="true"></div>

        <div class="journal-front">
            <div class="journal-columns">
                @foreach ($features as $entry)
                    <article
                        class="journal-story journal-reveal journal-reveal--up"
                        style="--portal-delay: {{ $loop->index * 0.08 }}s"
                        @if (! empty($entry['category_id'])) id="{{ $entry['category_id'] }}" @endif
                    >
                        <div class="journal-kicker">
                            <span>{{ $entry['tag'] }}</span>
                            <time datetime="{{ $entry['date'] }}">{{ $entry['date_label'] }}</time>
                        </div>

                        <a href="{{ $entry['href'] }}" class="journal-story-media" tabindex="-1" aria-hidden="true">
                            <img
                                src="{{ $entry['image'] }}"
                                alt=""
                                loading="lazy"
                                width="800"
                                height="520"
                                decoding="async"
                            >
                        </a>

                        <h3 class="journal-story-headline">
                            <a href="{{ $entry['href'] }}">{{ $entry['title'] }}</a>
                        </h3>
                        <p class="journal-story-excerpt">{{ $entry['excerpt'] }}</p>
                        <a href="{{ $entry['href'] }}" class="journal-continued">Read the dispatch</a>
                    </article>
                @endforeach
            </div>

            <aside class="journal-sidebar journal-reveal journal-reveal--up journal-reveal-delay-1" aria-label="Column &amp; briefs">
                @foreach ($columns as $entry)
                    <article class="journal-column-piece">
                        <div class="journal-kicker">
                            <span>{{ $entry['tag'] }}</span>
                            <time datetime="{{ $entry['date'] }}">{{ $entry['date_label'] }}</time>
                        </div>
                        <a href="{{ $entry['href'] }}" class="journal-column-media" tabindex="-1" aria-hidden="true">
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

                <div class="journal-briefs">
                    <h3 class="journal-briefs-title">In brief</h3>
                    <ul>
                        @foreach ($briefs as $entry)
                            <li>
                                <a href="{{ $entry['href'] }}" class="journal-brief-link">
                                    <span class="journal-brief-media">
                                        <img
                                            src="{{ $entry['image'] }}"
                                            alt=""
                                            loading="lazy"
                                            width="240"
                                            height="180"
                                            decoding="async"
                                        >
                                    </span>
                                    <span class="journal-brief-copy">
                                        <strong>{{ $entry['title'] }}</strong>
                                        <span>{{ $entry['excerpt'] }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <blockquote class="journal-pullquote">
                    Every place has a story.<br>
                    We just write it down.
                </blockquote>
            </aside>
        </div>

        @include('frontend.partials.journal.pagination')
    </div>
</section>
