@extends('layouts.search')

@seo($meta ?? null, 'Search — Coffee & Kala', 'Search articles, journals, poetries, gallery and studio pictures on Coffee & Kala.', 'search, Coffee & Kala')

@section('content')
    <div class="search-page">
        <div class="search-page-header">
            <p class="search-page-eyebrow">Search Results</p>
            <h1 class="search-page-title">
                @if ($query !== '')
                    Results for &ldquo;{{ $query }}&rdquo;
                @else
                    Search Coffee &amp; Kala
                @endif
            </h1>
        </div>

        @php
            $sectionsMeta = collect([
                ['key' => 'journal', 'label' => 'Journal', 'paginator' => $journal],
                ['key' => 'features', 'label' => 'Features', 'paginator' => $features],
                ['key' => 'poetry', 'label' => 'Poetry', 'paginator' => $poetry],
                ['key' => 'gallery', 'label' => 'Gallery', 'paginator' => $gallery],
                ['key' => 'studio', 'label' => 'Studio', 'paginator' => $studio],
            ])->filter(fn (array $section): bool => ($section['paginator']?->total() ?? 0) > 0)->values();

            $totalAll = $sectionsMeta->sum(fn (array $section): int => $section['paginator']->total());
        @endphp

        @if ($query === '')
            <p class="search-page-hint">Type in the search box above to find articles, journals, poetries, gallery and studio pictures.</p>
        @elseif ($sectionsMeta->isEmpty())
            <p class="search-page-hint">No results found for &ldquo;{{ $query }}&rdquo;. Try a different title.</p>
        @else
            @if ($sectionsMeta->count() > 1)
                <div class="search-tabs" role="tablist" aria-label="Filter results by category">
                    <button type="button" class="search-tab is-active" data-tab-target="all" role="tab" aria-selected="true">
                        All <span class="search-tab-count">{{ $totalAll }}</span>
                    </button>
                    @foreach ($sectionsMeta as $section)
                        <button type="button" class="search-tab" data-tab-target="{{ $section['key'] }}" role="tab" aria-selected="false">
                            {{ $section['label'] }} <span class="search-tab-count">{{ $section['paginator']->total() }}</span>
                        </button>
                    @endforeach
                </div>
            @endif

            @foreach ($sectionsMeta as $section)
                @include('frontend.partials.search.section', ['paginator' => $section['paginator'], 'label' => $section['label'], 'sectionKey' => $section['key']])
            @endforeach
        @endif
    </div>
@endsection
