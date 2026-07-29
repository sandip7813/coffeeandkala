{{-- Category boxes — icon tiles with slow cover image on hover --}}
<section class="features-mosaic" aria-label="Feature chapters">
    <header class="features-mosaic-head">
        <p class="features-eyebrow">Eight chapters</p>
        <h2 class="features-mosaic-title">Choose a door</h2>
    </header>

    <div class="features-mosaic-grid">
        @foreach ($categories as $category)
            <a
                href="{{ route('features.show', $category['id']) }}"
                class="features-tile"
                style="--tile-index: {{ $loop->index }}"
            >
                <span class="features-tile-media" aria-hidden="true">
                    <img
                        src="{{ $category['cover'] }}"
                        alt=""
                        class="features-tile-image"
                        loading="lazy"
                        width="700"
                        height="560"
                        decoding="async"
                    >
                </span>
                <span class="features-tile-veil" aria-hidden="true"></span>

                <span class="features-tile-number">{{ $category['number'] }}</span>
                <span class="features-tile-icon" aria-hidden="true">
                    <i class="fa-solid {{ $category['icon'] }}"></i>
                </span>
                <span class="features-tile-title">{{ $category['name'] }}</span>
                <span class="features-tile-lead">{{ $category['lead'] }}</span>
            </a>
        @endforeach
    </div>
</section>
