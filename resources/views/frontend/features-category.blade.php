@extends('layouts.features')

@section('title', $category['name'].' — Features — Coffee & Kala')

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
        ])
    </div>
@endsection
