{{-- Shared chapter strip + themed closing for category pages --}}
@include('frontend.partials.features.pagination', ['category' => $category])

<nav class="fc-siblings features-reveal features-reveal--up" aria-label="Other chapters">
    <div class="fc-siblings-inner">
        <p class="fc-siblings-label">Other chapters</p>
        <ul class="fc-siblings-list">
            @foreach ($categories as $sibling)
                <li>
                    <a
                        href="{{ route('features.show', $sibling['id']) }}"
                        @class([
                            'fc-siblings-link',
                            'is-current' => $sibling['id'] === $category['id'],
                        ])
                        @if ($sibling['id'] === $category['id']) aria-current="page" @endif
                    >
                        <span class="fc-siblings-num">{{ $sibling['number'] }}</span>
                        <span>{{ $sibling['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>

<section class="fc-closing features-reveal features-reveal--up">
    <blockquote class="fc-closing-quote">{{ $category['quote'] }}</blockquote>
    <div class="fc-closing-actions">
        <a href="{{ route('features') }}" class="fc-btn">All features</a>
        <a href="{{ route('journal') }}" class="fc-btn fc-btn--ghost">Journal</a>
        <a href="{{ route('gallery') }}" class="fc-btn fc-btn--ghost">Gallery</a>
    </div>
</section>
