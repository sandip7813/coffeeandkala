@php
    $mediaType = $section['media_type'] ?? null;
    $hasImage = $mediaType === 'image' && filled($section['image']);
    $hasVideo = $mediaType === 'video' && \App\Support\YoutubeEmbed::url($section['youtube_url'] ?? null) !== null;
    $hasGallery = $mediaType === 'gallery' && count($section['gallery']) > 0;

    if ($hasImage) {
        $beside = in_array($section['image_position'], ['left', 'right'], true) && $section['content_position'] === 'beside';
        $layout = $beside ? 'image-'.$section['image_position'] : 'image-top';
    } elseif ($hasVideo) {
        $layout = 'video-'.($section['youtube_position'] === 'right' ? 'right' : 'left');
    } elseif ($hasGallery) {
        $layout = 'gallery';
    } else {
        $layout = 'content-only';
    }

    $embedUrl = $hasVideo ? \App\Support\YoutubeEmbed::url($section['youtube_url']) : null;
    $videoThumbnail = $hasVideo ? \App\Support\YoutubeEmbed::thumbnailUrl($section['youtube_url']) : null;
    $companionType = $section['video_companion_type'] ?? 'none';
@endphp

<section
    id="{{ $section['id'] }}"
    @class([
        'article-section',
        'article-section--'.$layout,
        'article-section--media' => $hasImage || $hasVideo,
    ])
    style="--portal-delay: {{ $loop->index * 0.06 }}s"
>
    @if ($hasVideo && filled($section['heading']))
        {{-- Video sections put the title above the video/companion columns,
             spanning the full width, rather than squeezed into one side. --}}
        <h2 class="article-section-title-full">{{ $section['heading'] }}</h2>
    @endif

    @if ($hasImage)
        <div class="article-section-media">
            @if (in_array($layout, ['image-left', 'image-right'], true))
                {{-- Left/right sits beside the text, so the smaller
                     thumbnail is enough — the full image still opens in
                     Fancybox on click. --}}
                <span class="article-section-media-accent" aria-hidden="true"></span>
                <a href="{{ $section['image'] }}" data-fancybox="article-body">
                    <img src="{{ $section['image_thumb'] ?? $section['image'] }}" alt="" loading="lazy" width="700" height="520" decoding="async">
                </a>
            @else
                {{-- Center/standalone is the page's own showcase image —
                     the full large copy. --}}
                <a href="{{ $section['image'] }}" data-fancybox="article-body">
                    <img src="{{ $section['image'] }}" alt="" loading="lazy" width="1200" height="640" decoding="async">
                </a>
            @endif
        </div>
    @elseif ($hasVideo)
        <div class="article-section-media article-section-media-video">
            <a
                href="{{ $embedUrl }}?autoplay=1&rel=0"
                data-fancybox="article-video"
                data-type="iframe"
                data-width="960"
                data-height="540"
                class="article-section-video-trigger"
                aria-label="Play video{{ $section['heading'] ? ': '.$section['heading'] : '' }}"
            >
                <img src="{{ $videoThumbnail }}" alt="{{ $section['heading'] ?: 'Video' }}" loading="lazy">
                <span class="article-section-video-play" aria-hidden="true">
                    <i class="fa-solid fa-play"></i>
                </span>
                <span class="article-section-video-label" aria-hidden="true">
                    <i class="fa-brands fa-youtube"></i> Watch the video
                </span>
            </a>
        </div>
    @endif

    <div class="article-section-body">
        @if (filled($section['heading']) && ! $hasVideo)
            <h2>{{ $section['heading'] }}</h2>
        @endif

        @if ($hasVideo && $companionType === 'image' && filled($section['video_companion_image']))
            {{-- "An Image" companion replaces the Content text on the video's
                 other side entirely. --}}
            <a href="{{ $section['video_companion_image'] }}" data-fancybox="article-body" class="article-section-video-companion-image">
                <img src="{{ $section['video_companion_image'] }}" alt="" loading="lazy">
            </a>
        @else
            {{-- "The Content below" and "Nothing" both fall through to here —
                 Content is common to every section regardless of media type,
                 so an empty companion choice just means there's nothing
                 else to show beside the video beyond it. --}}
            <div class="article-section-content">
                {!! $section['content_html'] !!}
            </div>
        @endif

        @if ($hasGallery)
            <div class="article-section-gallery">
                @foreach ($section['gallery'] as $galleryImage)
                    <a href="{{ $galleryImage['image'] }}" data-fancybox="article-section-gallery-{{ $section['id'] }}" data-caption="{{ $galleryImage['caption'] }}" class="article-section-gallery-item">
                        <img src="{{ $galleryImage['thumb'] }}" alt="" loading="lazy">
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
