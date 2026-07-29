@extends('layouts.features')

@section('title', 'Features — Coffee & Kala')

@section('content')
    <div class="features-page features-page--index">
        @include('frontend.partials.features.banner')
        @include('frontend.partials.features.chapters', ['categories' => $categories])
        @include('frontend.partials.features.closing')
    </div>
@endsection
