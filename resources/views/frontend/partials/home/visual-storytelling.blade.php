{{-- SECTION 05: Features — picked by a super admin in Admin > Home Page
     Sections (see App\Support\HomeSections); hidden entirely when nothing
     has been picked. --}}
@if (! empty($stories))
    <section
        id="sec-05"
        class="section-visual-feature"
        aria-label="Features"
        data-visual-feature
    >
        <div class="visual-feature-header">
            <h2 class="visual-feature-section-title">Features</h2>
            <a href="{{ route('features') }}" class="visual-feature-section-link" aria-label="Unfold the stories">
                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
        </div>

        <div class="visual-feature-embla">
            <div class="visual-feature-viewport" data-visual-feature-viewport>
                <div class="visual-feature-container">
                    @foreach ($stories as $index => $story)
                        <article class="visual-feature-slide @if ($index === 0) is-active @endif">
                            <div class="visual-feature-rule visual-feature-rule--top" aria-hidden="true"></div>

                            <div class="visual-feature-block">
                                <div class="visual-feature-media">
                                    <img src="{{ $story['image'] }}" alt="{{ $story['title'] }}" loading="lazy" class="visual-feature-media-img">
                                </div>

                                <div class="visual-feature-panel">
                                    <p class="visual-feature-meta">
                                        <time datetime="{{ $story['date'] }}">{{ $story['date_label'] }}</time>
                                        <span aria-hidden="true">•</span>
                                        <span>{{ $story['category_name'] }}</span>
                                    </p>
                                    <span class="visual-feature-accent" aria-hidden="true"></span>
                                    <h3 class="visual-feature-title">{{ $story['title'] }}</h3>
                                    <p class="visual-feature-excerpt">{{ Str::limit($story['excerpt'], 220) }}</p>
                                    <a href="{{ $story['href'] }}" class="visual-feature-cta">
                                        Continue reading
                                        <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="visual-feature-rule visual-feature-rule--bottom" aria-hidden="true"></div>
                        </article>
                    @endforeach
                </div>
            </div>

            @if (count($stories) > 1)
                <button type="button" class="visual-feature-nav visual-feature-nav--prev" data-visual-feature-prev aria-label="Previous story">
                    <svg class="visual-feature-nav-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M28 12L16 24L28 36" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 24H36" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </button>
                <button type="button" class="visual-feature-nav visual-feature-nav--next" data-visual-feature-next aria-label="Next story">
                    <svg class="visual-feature-nav-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M20 12L32 24L20 36" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 24H30" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </button>
            @endif
        </div>
    </section>
@endif
