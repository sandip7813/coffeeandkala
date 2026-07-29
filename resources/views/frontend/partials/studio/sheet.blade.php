{{-- Exhibition wall — framed works on a light studio surface --}}
<section class="studio-wall" aria-label="Art exhibition wall">
    <div id="galleryLightRoot" class="studio-wall-surface studio-reveal studio-reveal--media">
        @foreach ($works as $work)
            <figure class="studio-work">
                <a
                    href="{{ $work['src'] }}"
                    class="studio-work-link"
                    data-galleria-index="{{ $loop->index }}"
                    aria-label="Open {{ $work['title'] }}"
                >
                    <span class="studio-photo-frame">
                        <span class="studio-photo-frame-edge" aria-hidden="true"></span>
                        <span class="studio-photo-frame-corners" aria-hidden="true">
                            <i></i><i></i><i></i><i></i>
                        </span>
                        <span class="studio-photo-rabbet">
                            <span class="studio-photo-mat">
                                <img
                                    src="{{ $work['thumb'] }}"
                                    alt="{{ $work['title'] }}"
                                    loading="lazy"
                                    width="400"
                                    height="500"
                                >
                            </span>
                        </span>
                    </span>
                    <span class="studio-work-number" aria-hidden="true">{{ $work['number'] }}</span>
                </a>

                <figcaption class="studio-work-caption">
                    <a
                        href="{{ $work['src'] }}"
                        class="studio-work-title"
                        data-galleria-index="{{ $loop->index }}"
                    >{{ $work['title'] }}</a>
                    @if (! empty($work['medium']))
                        <span class="studio-work-medium">{{ $work['medium'] }}</span>
                    @endif
                </figcaption>
            </figure>
        @endforeach
    </div>
</section>

{{-- Fullscreen Galleria viewer — whitish studio theme --}}
<div
    id="galleriaModal"
    class="galleria-modal galleria-modal--studio"
    hidden
    role="dialog"
    aria-modal="true"
    aria-label="Artwork viewer"
>
    <button type="button" class="galleria-modal-close" id="galleriaModalClose" aria-label="Close gallery">
        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
    </button>
    <div id="galleriaInstance" class="galleria"></div>
</div>
