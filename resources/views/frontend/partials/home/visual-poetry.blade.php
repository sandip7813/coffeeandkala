{{-- SECTION 05: GALLERY OF VISUAL POETRY — Studio Embla --}}
@php
    $studioSlides = [
        [
            'title' => 'Indigo Wash',
            'medium' => 'Watercolour on cotton',
            'image' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=900',
        ],
        [
            'title' => 'Clay Conversation',
            'medium' => 'Ceramic sculpture',
            'image' => 'https://images.unsplash.com/photo-1605721911519-3dfeb3be25e7?q=80&w=900',
        ],
        [
            'title' => 'Quiet Marks',
            'medium' => 'Graphite on paper',
            'image' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?q=80&w=900',
        ],
        [
            'title' => 'Warm Geometry',
            'medium' => 'Acrylic study',
            'image' => 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?q=80&w=900',
        ],
        [
            'title' => 'Studio Light',
            'medium' => 'Light study',
            'image' => 'https://images.unsplash.com/photo-1482160549825-59d1b23cb208?q=80&w=900',
        ],
        [
            'title' => 'Threaded Colour',
            'medium' => 'Textile collage',
            'image' => 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?q=80&w=900',
        ],
    ];
@endphp

<section id="sec-05" class="section-home-carousel section-visual-poetry" aria-label="Gallery of visual poetry">
    <div class="home-carousel-header animate-on-scroll animate-on-scroll--text">
        <div class="home-carousel-heading">
            <h2 class="home-carousel-title">Gallery of visual poetry</h2>
        </div>
        <a href="{{ route('studio') }}" class="home-carousel-link">Explore Studio <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i></a>
    </div>

    <div class="home-embla home-embla--studio" data-home-carousel data-slides-visible="4">
        <div class="home-embla__viewport">
            <div class="home-embla__container">
                @foreach ($studioSlides as $slide)
                    <article class="home-embla__slide">
                        <a href="{{ route('studio') }}" class="home-studio-card">
                            <div class="home-studio-card-frame">
                                <div class="home-studio-card-media" style="background-image: url('{{ $slide['image'] }}')"></div>
                            </div>
                            <div class="home-studio-card-body">
                                <h3 class="home-studio-card-title">{{ $slide['title'] }}</h3>
                                <span class="home-studio-card-medium">{{ $slide['medium'] }}</span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
        <button type="button" class="home-embla-arrow home-embla-arrow--prev" data-home-prev aria-label="Previous">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
        </button>
        <button type="button" class="home-embla-arrow home-embla-arrow--next" data-home-next aria-label="Next">
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </button>
    </div>
</section>
