@extends('layouts.poetry')

@section('title', 'The Poetry Collection — Coffee & Kala')

@section('content')
    <div class="poetry-page">
        @include('frontend.partials.poetry.hero')
        @include('frontend.partials.poetry.intro', ['poems' => $poems])
        @include('frontend.partials.poetry.collection', ['poems' => $poems])
        @include('frontend.partials.poetry.closing')
    </div>
@endsection
