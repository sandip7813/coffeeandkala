{{-- Poster wall — uniform posters on brick wall --}}
<section class="gallery-wall" aria-label="Poster wall">
    <div id="galleryLightRoot" class="gallery-wall-surface gallery-reveal gallery-reveal--media">
        @foreach ($plates as $plate)
            <figure class="gallery-poster">
                <a
                    href="{{ $plate['src'] }}"
                    class="gallery-poster-link"
                    data-galleria-index="{{ $loop->index }}"
                    aria-label="Open {{ $plate['title'] }}"
                >
                    <span class="gallery-poster-sheet">
                        <img
                            src="{{ $plate['thumb'] }}"
                            alt="{{ $plate['title'] }}"
                            loading="lazy"
                            width="400"
                            height="500"
                        >
                    </span>
                </a>

                <figcaption class="gallery-poster-caption">
                    <a
                        href="{{ $plate['src'] }}"
                        class="gallery-poster-title"
                        data-galleria-index="{{ $loop->index }}"
                    >{{ $plate['title'] }}</a>
                    @if (! empty($plate['location']))
                        <span class="gallery-poster-place">{{ $plate['location'] }}</span>
                    @endif
                </figcaption>
            </figure>
        @endforeach
    </div>
</section>

{{-- Fullscreen Galleria viewer --}}
<div
    id="galleriaModal"
    class="galleria-modal"
    hidden
    role="dialog"
    aria-modal="true"
    aria-label="Photograph viewer"
>
    <button type="button" class="galleria-modal-close" id="galleriaModalClose" aria-label="Close gallery">
        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
    </button>
    <div id="galleriaInstance" class="galleria"></div>
</div>
