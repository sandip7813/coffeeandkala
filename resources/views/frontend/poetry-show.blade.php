@extends('layouts.poetry')

@section('title', $current['title'].' — The Poetry Shelf — Coffee & Kala')

@php
    $pageCount = 3 + count($poems) * 2 + 1; // cover + (contents ×2) + (poem ×2 each) + closing
    $currentIndex = array_search($current['slug'], array_column($poems, 'slug'), true);
    $startPage = 3 + $currentIndex * 2;

    // Every poem's own card (left) page, keyed by slug — used both by the
    // contents list and by each card's "nearby poems" index below.
    $cardPageBySlug = [];
    foreach ($poems as $i => $p) {
        $cardPageBySlug[$p['slug']] = 3 + $i * 2;
    }
@endphp

@section('content')
    <div class="poetry-page poetry-book-shell">
        <nav class="poetry-book-topbar" aria-label="Book navigation">
            <a href="{{ route('poetry') }}" class="poetry-book-exit">
                <i class="fa-solid fa-arrow-left-long" aria-hidden="true"></i>
                Close the book
            </a>
            <p class="poetry-book-progress">
                Page <span data-poetry-book-position>1</span> of <span data-poetry-book-total>{{ $pageCount }}</span>
            </p>
            <button type="button" class="poetry-book-toc-btn" data-poetry-book-jump="1">
                Contents
            </button>
        </nav>

        <div
            class="poetry-book-stage"
            data-poetry-book
            data-start="{{ $startPage }}"
        >
            <div class="poetry-book" data-poetry-book-frame>

                {{-- Page 0 — Cover --}}
                <div class="poetry-book-page poetry-book-page--cover" data-density="hard">
                    <p class="poetry-eyebrow">Coffee &amp; Kala · Poetry</p>
                    <h1 class="poetry-book-cover-title">The Poetry Shelf</h1>
                    <p class="poetry-book-cover-tagline">a bound collection, kept for slow reading</p>
                    <span class="poetry-ornament"></span>
                    <p class="poetry-book-cover-copy">
                        {{ count($poems) }} poems, gathered for slow reading. Open the contents the way
                        you would open the front matter of an old anthology — and read on, at your own pace.
                    </p>
                    <button type="button" class="poetry-cta" data-poetry-book-next>
                        Enter
                        <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                    </button>
                </div>

                {{-- Page 1 — Contents (left) --}}
                <div class="poetry-book-page poetry-book-page--vined">
                    @include('frontend.partials.poetry.vine')
                    <div class="poetry-book-page-inner">
                        <p class="poetry-eyebrow">Contents</p>
                        <h2 class="poetry-book-page-title">Where would you<br>like to begin?</h2>
                        <span class="poetry-ornament"></span>
                        <p class="poetry-book-page-copy">
                            Turn the page to read in order, or choose a poem from the list — each one
                            is its own room, and none of them mind being visited out of turn.
                        </p>
                    </div>
                </div>

                {{-- Page 2 — Contents (right) --}}
                <div class="poetry-book-page">
                    <ol class="poetry-book-toc">
                        @foreach ($poems as $i => $p)
                            <li class="poetry-book-toc-row">
                                <button type="button" class="poetry-book-toc-link" data-poetry-book-jump="{{ 3 + $i * 2 }}">
                                    <span class="poetry-book-toc-number">{{ $p['number'] }}</span>
                                    <span class="poetry-book-toc-title">{{ $p['title'] }}</span>
                                    <span class="poetry-book-toc-leader" aria-hidden="true"></span>
                                    <span class="poetry-book-toc-page">{{ $i + 1 }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ol>
                </div>

                {{-- Pages 3.. — a card + the poem itself, per poem --}}
                @foreach ($poems as $i => $p)
                    <div class="poetry-book-page poetry-book-page--vined poetry-book-page--card" data-poem-slug="{{ $p['slug'] }}">
                        @include('frontend.partials.poetry.vine')
                        <div class="poetry-book-page-inner poetry-book-page-inner--index">

                            <p class="poetry-eyebrow">From the shelf</p>
                            <h3 class="poetry-book-index-heading">More poems nearby</h3>
                            <span class="poetry-ornament"></span>

                            <ol class="poetry-book-card-index poetry-book-card-index--full">
                                <li class="poetry-book-index-label poetry-book-index-label--start" aria-hidden="true">
                                    <span class="poetry-book-index-label-mark"></span>
                                    <span class="poetry-book-index-label-text">Previously</span>
                                </li>
                                @foreach ($p['nearby']['prev'] as $np)
                                    <li>
                                        <button type="button" class="poetry-book-index-link" data-poetry-book-jump="{{ $cardPageBySlug[$np['slug']] }}">
                                            <span class="poetry-book-index-photo">
                                                <img src="{{ $np['thumb'] }}" alt="" loading="lazy">
                                            </span>
                                            <span class="poetry-book-index-info">
                                                <span class="poetry-book-index-kicker">{{ $np['mood'] }}</span>
                                                <span class="poetry-book-index-title">{{ $np['title'] }}</span>
                                            </span>
                                        </button>
                                    </li>
                                @endforeach
                                <li class="poetry-book-index-label poetry-book-index-label--mid" aria-hidden="true">
                                    <span class="poetry-book-index-label-mark"></span>
                                    <span class="poetry-book-index-label-text">Coming up</span>
                                </li>
                                @foreach ($p['nearby']['next'] as $np)
                                    <li>
                                        <button type="button" class="poetry-book-index-link" data-poetry-book-jump="{{ $cardPageBySlug[$np['slug']] }}">
                                            <span class="poetry-book-index-photo">
                                                <img src="{{ $np['thumb'] }}" alt="" loading="lazy">
                                            </span>
                                            <span class="poetry-book-index-info">
                                                <span class="poetry-book-index-kicker">{{ $np['mood'] }}</span>
                                                <span class="poetry-book-index-title">{{ $np['title'] }}</span>
                                            </span>
                                        </button>
                                    </li>
                                @endforeach
                            </ol>

                        </div>
                    </div>

                    <div class="poetry-book-page poetry-book-page--poem">
                        <p class="poetry-book-number-mark" aria-hidden="true">{{ $p['number'] }}</p>
                        <h2 class="poetry-book-poem-title">{{ $p['title'] }}</h2>
                        <span class="poetry-ornament"></span>
                        <div class="poetry-book-poem-body">
                            @foreach ($p['stanzas'] as $stanza)
                                <p class="poetry-stanza">
                                    @foreach ($stanza as $line)
                                        {{ $line }}<br>
                                    @endforeach
                                </p>
                            @endforeach
                        </div>
                        <p class="poetry-book-page-number">{{ $i + 1 }}</p>
                    </div>
                @endforeach

                {{-- Last page — Closing --}}
                <div class="poetry-book-page poetry-book-page--closing" data-density="hard">
                    <p class="poetry-eyebrow">The end — for now</p>
                    <h2 class="poetry-book-cover-title poetry-book-cover-title--small">Lean into the book<br>to read more heartfelt poems.</h2>
                    <span class="poetry-ornament"></span>
                    <blockquote class="poetry-book-closing-quote">
                        The rest of the pages are still waiting to be written.
                    </blockquote>
                    <div class="poetry-book-closing-actions">
                        <button type="button" class="poetry-cta" data-poetry-book-jump="1">
                            Back to contents
                            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                        </button>
                        <a href="{{ route('poetry') }}" class="poetry-cta poetry-cta--ghost">
                            Return to the collection
                        </a>
                    </div>
                </div>

            </div>

            <span class="poetry-book-edge poetry-book-edge--left" data-poetry-book-edge="left" aria-hidden="true">
                <span class="poetry-book-edge-leaves"></span>
                <span class="poetry-book-edge-gilt"></span>
            </span>
            <span class="poetry-book-edge poetry-book-edge--right" data-poetry-book-edge="right" aria-hidden="true">
                <span class="poetry-book-edge-leaves"></span>
                <span class="poetry-book-edge-gilt"></span>
            </span>

            <button type="button" class="poetry-book-nav poetry-book-nav--prev" data-poetry-book-prev aria-label="Previous page">
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </button>
            <button type="button" class="poetry-book-nav poetry-book-nav--next" data-poetry-book-next aria-label="Next page">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </button>
        </div>
    </div>
@endsection
