@extends('layouts.app')

@seo($meta ?? null, 'COFFEE & KALA — Editorial Journal', 'Coffee & Kala is an editorial journal of slow stories, visual poetry, and everyday ritual — Features, Journal, Studio, Gallery, and Poetry in one place.', 'Coffee & Kala, editorial journal, coffee culture, visual storytelling, poetry')

@php
    // The left sidebar's numbered nav mirrors the page's actual sections —
    // sec-03/04/05/07 are the dynamic, admin-picked homepage sections (see
    // App\Support\HomeSections) and drop out of this list entirely (not just
    // hidden) whenever nothing has been picked for them, same as the section
    // itself. The rest of the page is always present.
    $sidebarItems = collect([
        ['target' => 'sec-01', 'title' => 'The Home Of Slow Publishing', 'show' => true],
        ['target' => 'sec-02', 'title' => 'Thought of the day', 'show' => true],
        ['target' => 'sec-03', 'title' => 'The Edit', 'desc' => 'Latest editorial selection', 'show' => ! empty($latestPieces)],
        ['target' => 'sec-04', 'title' => 'Short Reads', 'desc' => 'Brief stories, ideas & observations', 'show' => ! empty($theSelection)],
        ['target' => 'sec-05', 'title' => 'Features', 'desc' => 'A closer look', 'show' => ! empty($homeFeatures)],
        ['target' => 'sec-06', 'title' => 'Gallery', 'desc' => 'A collection of photography & visual expression', 'show' => ! empty($homeGallery)],
        ['target' => 'sec-07', 'title' => 'The Journal', 'desc' => 'Perspectives on everything between', 'show' => ! empty($homeJournal)],
        ['target' => 'sec-08', 'title' => 'Studio', 'desc' => 'A collection of art & creative expression', 'show' => ! empty($homeStudio)],
        ['target' => 'sec-09', 'title' => 'Poetry', 'desc' => 'Words, Verses & Stories', 'show' => true],
    ])->filter(fn (array $item): bool => $item['show'])->values();
@endphp

@section('sidebar')
    @foreach ($sidebarItems as $index => $item)
        <li>
            <a href="javascript:void(0)" class="nav-item @if ($loop->first) active @endif" data-target="{{ $item['target'] }}">
                <span class="nav-item-number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                <div class="nav-item-title">{{ $item['title'] }}</div>
                @if (! empty($item['desc']))
                    <div class="nav-item-desc">{{ $item['desc'] }}</div>
                @endif
                <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
            </a>
        </li>
    @endforeach
@endsection

@section('content')
    @include('frontend.partials.home.hero')
    @include('frontend.partials.home.quote')
    @include('frontend.partials.home.featured', ['stories' => $latestPieces])
    @include('frontend.partials.home.three-small-stories', ['stories' => $theSelection])
    @include('frontend.partials.home.visual-storytelling', ['stories' => $homeFeatures])
    @include('frontend.partials.home.gallery-visual-storytelling', ['slides' => $homeGallery])
    @include('frontend.partials.home.journal-carousel', ['posts' => $homeJournal])
    @include('frontend.partials.home.visual-poetry', ['slides' => $homeStudio])
    @include('frontend.partials.home.poetry')
@endsection
