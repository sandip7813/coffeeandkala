@extends('layouts.about')

@section('title', 'Our Story — Coffee & Kala')

@section('content')
    <div class="about-page">
        @include('frontend.partials.about.banner')
        @include('frontend.partials.about.split-image-left')
        @include('frontend.partials.about.split-image-right')
        @include('frontend.partials.about.stack-image-top')
        @include('frontend.partials.about.stack-content-top')
        @include('frontend.partials.about.closing')
    </div>
@endsection
