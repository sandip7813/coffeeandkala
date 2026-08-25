@extends('layouts.features')

@section('title', 'Features — Coffee & Kala')

@section('content')
    <div class="features-page features-page--index">
        @include('frontend.partials.features.intro')
        @include('frontend.partials.features.edition', ['entries' => $entries, 'categories' => $categories])
        @include('frontend.partials.features.closing')
    </div>
@endsection
