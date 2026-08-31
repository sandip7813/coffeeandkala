@php
    $hasMedia = $section['image'] !== null;
@endphp

<section
    id="{{ $section['id'] }}"
    @class([
        'article-section',
        'article-section--'.$section['layout'],
        'article-section--media' => $hasMedia,
    ])
    style="--portal-delay: {{ $loop->index * 0.06 }}s"
>
    @if ($hasMedia)
        <div class="article-section-media">
            @if (in_array($section['layout'], ['image-left', 'image-right'], true))
                <span class="article-section-media-accent" aria-hidden="true"></span>
                <a href="{{ $section['image'] }}" data-fancybox="article-body">
                    <img
                        src="{{ $section['image'] }}"
                        alt=""
                        loading="lazy"
                        width="700"
                        height="520"
                        decoding="async"
                    >
                </a>
            @else
                <img
                    src="{{ $section['image'] }}"
                    alt=""
                    loading="lazy"
                    width="1200"
                    height="640"
                    decoding="async"
                >
            @endif
        </div>
    @endif

    <div class="article-section-body">
        <h2>{{ $section['heading'] }}</h2>
        @foreach ($section['body'] as $paragraph)
            <p>{{ $paragraph }}</p>
        @endforeach
    </div>
</section>
