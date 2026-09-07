@extends('layouts.poetry')

@seo($meta ?? null, 'The Poetry Collection — Coffee & Kala', 'A home in every poem — the Coffee & Kala poetry collection.', 'poetry, poems, collection, Coffee & Kala')

@section('content')
    <div class="poetry-page">
        @include('frontend.partials.poetry.hero')
        @include('frontend.partials.poetry.intro', ['poems' => $poems])
        @if (! empty($poems))
            @include('frontend.partials.poetry.collection', ['poems' => $poems])
        @else
            <p class="poetry-empty">No poetry found.</p>
        @endif
        @include('frontend.partials.poetry.closing')
    </div>
@endsection
