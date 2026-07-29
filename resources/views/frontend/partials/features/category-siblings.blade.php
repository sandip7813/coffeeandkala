<nav class="features-siblings features-reveal features-reveal--up" aria-label="Other chapters">
    <div class="features-siblings-inner">
        <p class="features-eyebrow features-eyebrow--muted">Continue wandering</p>
        <ul class="features-siblings-list">
            @foreach ($categories as $sibling)
                <li>
                    <a
                        href="{{ route('features.show', $sibling['id']) }}"
                        @class([
                            'features-sibling-link',
                            'is-current' => $sibling['id'] === $category['id'],
                        ])
                        @if ($sibling['id'] === $category['id']) aria-current="page" @endif
                    >
                        <span class="features-sibling-number">{{ $sibling['number'] }}</span>
                        <span class="features-sibling-name">{{ $sibling['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>
