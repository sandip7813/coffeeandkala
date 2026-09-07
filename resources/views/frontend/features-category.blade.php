@extends('layouts.features')

@seo($meta ?? null, $category['name'].' — Features — Coffee & Kala', $category['name'].' — in-depth Features stories on coffee culture and craft from Coffee & Kala.', $category['name'].', features, Coffee & Kala')

@section('content')
    <div
        @class([
            'features-page',
            'features-page--category',
            'features-theme',
            'features-theme--'.$category['id'],
        ])
    >
        @include('frontend.partials.features.themes.'.$category['id'], [
            'category' => $category,
            'categories' => $categories,
            'entries' => $entries,
        ])
    </div>
@endsection
