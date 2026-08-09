{{-- SECTION 05: Features Unfolded — featured article slider --}}
@php
    $visualStories = [
        [
            'date' => '12 Mar 2026',
            'category' => 'Features',
            'title' => 'At daybreak of the fifteenth day of my search',
            'excerpt' => 'When the amphitheater had cleared I crept stealthily to the top and as the great excavation lay open in the morning light, the city below still held its breath…',
            'image' => 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?q=80&w=1800',
        ],
        [
            'date' => '28 Feb 2026',
            'category' => 'Travel',
            'title' => 'Letters written between stations',
            'excerpt' => 'Windows blur into watercolour. Somewhere between platforms, a slower story finds its pace — and asks only that you stay with it a little longer…',
            'image' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=1800',
        ],
        [
            'date' => '14 Feb 2026',
            'category' => 'Culture',
            'title' => 'The colour of quiet markets',
            'excerpt' => 'Spice, cloth, and conversation gather like a living collage. What begins as a glance becomes a morning you are invited to finish reading…',
            'image' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=1800',
        ],
        [
            'date' => '02 Feb 2026',
            'category' => 'Lifestyle',
            'title' => 'Brewing between unfinished pages',
            'excerpt' => 'Steam rises over sentences still drying. The day begins before the world asks for anything — and the next paragraph waits just beyond the fold…',
            'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1800',
        ],
    ];
@endphp

<section
    id="sec-05"
    class="section-visual-feature"
    aria-label="Features Unfolded"
    data-visual-feature
>
    <div class="visual-feature-header">
        <h2 class="visual-feature-section-title">Features Unfolded</h2>
        <a href="{{ route('features') }}" class="visual-feature-section-link">
            Unfold the Stories
            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
        </a>
    </div>

    <div class="visual-feature-embla">
        <div class="visual-feature-viewport" data-visual-feature-viewport>
            <div class="visual-feature-container">
                @foreach ($visualStories as $index => $story)
                    <article class="visual-feature-slide @if ($index === 0) is-active @endif">
                        <div
                            class="visual-feature-media"
                            style="background-image: url('{{ $story['image'] }}')"
                            role="img"
                            aria-label="{{ $story['title'] }}"
                        ></div>
                        <div class="visual-feature-shade" aria-hidden="true"></div>

                        <div class="visual-feature-stage">
                            <div class="visual-feature-panel-stack">
                                <div class="visual-feature-rule visual-feature-rule--top" aria-hidden="true"></div>

                                <div class="visual-feature-panel">
                                    <p class="visual-feature-meta">
                                        <time datetime="{{ $story['date'] }}">{{ $story['date'] }}</time>
                                        <span aria-hidden="true">•</span>
                                        <span>{{ $story['category'] }}</span>
                                    </p>
                                    <span class="visual-feature-accent" aria-hidden="true"></span>
                                    <h3 class="visual-feature-title">{{ $story['title'] }}</h3>
                                    <p class="visual-feature-excerpt">{{ $story['excerpt'] }}</p>
                                    <a href="{{ route('features') }}" class="visual-feature-cta">
                                        Continue reading
                                        <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                                    </a>
                                </div>

                                <div class="visual-feature-rule visual-feature-rule--bottom" aria-hidden="true"></div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

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
    </div>
</section>
