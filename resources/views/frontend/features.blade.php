@extends('layouts.features')

@seo($meta ?? null, 'Features — Coffee & Kala', 'In-depth features on coffee culture, craft, and the people behind it — from Coffee & Kala.', 'features, coffee culture, essays, Coffee & Kala')

@section('content')
    <div class="features-page features-page--index">
        @include('frontend.partials.features.intro')
        @include('frontend.partials.features.edition', [
            'highlighted' => $highlighted,
            'edition' => $edition,
            'recentFeatures' => $recentFeatures,
            'categories' => $categories,
        ])
        @include('frontend.partials.features.closing')
    </div>
@endsection
