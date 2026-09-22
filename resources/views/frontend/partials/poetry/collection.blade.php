{{-- THE COLLECTION — a corridor of arched doors, one per poem. Each door
     opens straight into the poem's own reading room. --}}
<section class="poetry-collection" aria-label="All poems">
    <ol class="poetry-collection-grid">
        @foreach ($poems as $poem)
            <li class="poetry-door-tile">
                <a
                    href="{{ route('poetry.show', $poem['slug']) }}"
                    class="poetry-door poetry-reveal poetry-reveal--up"
                    style="--portal-delay: {{ $loop->index * 0.06 }}s"
                >
                    <span class="poetry-door-frame">
                        <span class="poetry-door-photo-mask">
                            <img
                                src="{{ $poem['thumb'] }}"
                                alt=""
                                loading="lazy"
                                width="643"
                                height="1254"
                                decoding="async"
                                class="poetry-door-photo"
                            >
                        </span>
                        <span class="poetry-door-sill" aria-hidden="true"></span>
                        <img
                            src="{{ asset('images/poetry/door-frame.png') }}"
                            alt=""
                            loading="lazy"
                            width="643"
                            height="1254"
                            decoding="async"
                            class="poetry-door-graphic"
                        >
                        <span class="poetry-door-number" aria-hidden="true">{{ $poem['number'] }}</span>
                    </span>

                    <span class="poetry-door-title">{{ $poem['title'] }}</span>
                    <span class="poetry-door-excerpt">{{ $poem['excerpt'] }}</span>

                    <span class="poetry-door-meta">
                        <span class="poetry-door-enter">
                            Enter
                            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                        </span>
                    </span>
                </a>
            </li>
        @endforeach
    </ol>

    @include('frontend.partials.poetry.pagination', ['poems' => $poems])
</section>
