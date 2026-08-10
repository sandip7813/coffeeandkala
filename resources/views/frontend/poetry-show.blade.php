@extends('layouts.poetry')

@section('title', $poem['title'].' — The Poetry Collection — Coffee & Kala')

@section('content')
    <div class="poetry-page poetry-reader">
        <nav class="poetry-reader-bar" aria-label="Reading navigation">
            <a href="{{ route('poetry') }}" class="poetry-reader-back">
                <i class="fa-solid fa-arrow-left-long" aria-hidden="true"></i>
                Back to the collection
            </a>
            <span class="poetry-reader-count">{{ $poem['number'] }} / {{ sprintf('%02d', $total) }}</span>
        </nav>

        <article class="poetry-room" aria-labelledby="poetryReaderTitle">
            <span class="poetry-room-arch" aria-hidden="true"></span>

            <header class="poetry-room-header poetry-reveal poetry-reveal--up">
                <p class="poetry-room-mood">{{ $poem['mood'] }}</p>
                <h1 id="poetryReaderTitle" class="poetry-room-title">{{ $poem['title'] }}</h1>
                <span class="poetry-ornament" aria-hidden="true"></span>
            </header>

            <div class="poetry-room-body poetry-reveal poetry-reveal--up">
                @foreach ($poem['stanzas'] as $stanza)
                    <p class="poetry-stanza">
                        @foreach ($stanza as $line)
                            {{ $line }}<br>
                        @endforeach
                    </p>
                @endforeach
            </div>

            <p class="poetry-room-colophon poetry-reveal poetry-reveal--up">
                Coffee &amp; Kala — The Poetry Collection
            </p>
        </article>

        <nav class="poetry-turn" aria-label="More poems">
            <a href="{{ route('poetry.show', $prev['slug']) }}" class="poetry-turn-link poetry-turn-link--prev">
                <span class="poetry-turn-direction">
                    <i class="fa-solid fa-arrow-left-long" aria-hidden="true"></i>
                    Previous poem
                </span>
                <span class="poetry-turn-title">{{ $prev['title'] }}</span>
            </a>
            <a href="{{ route('poetry.show', $next['slug']) }}" class="poetry-turn-link poetry-turn-link--next">
                <span class="poetry-turn-direction">
                    Next poem
                    <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                </span>
                <span class="poetry-turn-title">{{ $next['title'] }}</span>
            </a>
        </nav>
    </div>
@endsection
