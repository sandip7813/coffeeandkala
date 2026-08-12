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
            <img
                src="{{ $section['image'] }}"
                alt=""
                loading="lazy"
                width="{{ $section['layout'] === 'image-top' ? 1200 : 700 }}"
                height="{{ $section['layout'] === 'image-top' ? 640 : 520 }}"
                decoding="async"
            >
        </div>
    @endif

    <div class="article-section-body">
        <h2>{{ $section['heading'] }}</h2>
        @foreach ($section['body'] as $paragraph)
            <p>{{ $paragraph }}</p>
        @endforeach
    </div>
</section>
