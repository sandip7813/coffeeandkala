{{-- SECTION 08: GALLERY OF VISUAL POETRY — Studio carousel, picked by a
     super admin from the Studio admin list/edit page (see
     App\Support\HomeMediaSections); hidden entirely when nothing has been
     picked. --}}
@if (! empty($slides))
    <section id="sec-08" class="section-home-carousel section-visual-poetry" aria-label="Gallery of visual poetry">
        <div class="home-carousel-header animate-on-scroll animate-on-scroll--text">
            <div class="home-carousel-heading">
                <h2 class="home-carousel-title">Studio</h2>
            </div>
            <a href="{{ route('studio') }}" class="home-carousel-link" aria-label="Explore Studio">
                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
        </div>

        <div class="home-embla home-embla--studio" data-home-carousel data-slides-visible="4">
            <div class="home-embla__viewport">
                <div class="home-embla__container">
                    @foreach ($slides as $slide)
                        <article class="home-embla__slide">
                            <a href="{{ route('studio') }}" class="home-studio-card">
                                <div class="home-studio-card-frame">
                                    <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" loading="lazy" class="home-studio-card-media">
                                </div>
                                <div class="home-studio-card-body">
                                    <h3 class="home-studio-card-title">{{ $slide['title'] }}</h3>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
            @if (count($slides) > 1)
                <button type="button" class="home-embla-arrow home-embla-arrow--prev" data-home-prev aria-label="Previous">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                </button>
                <button type="button" class="home-embla-arrow home-embla-arrow--next" data-home-next aria-label="Next">
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </button>
            @endif
        </div>
    </section>
@endif
