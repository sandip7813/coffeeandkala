@extends('layouts.app')

@section('title', 'COFFEE & KALA — Editorial Journal')

@section('content')
    @include('frontend.partials.home.hero')
    @include('frontend.partials.home.quote')
    @include('frontend.partials.home.featured')
    @include('frontend.partials.home.three-small-stories')
    @include('frontend.partials.home.visual-storytelling')
    @include('frontend.partials.home.gallery-visual-storytelling')
    @include('frontend.partials.home.journal-carousel')
    @include('frontend.partials.home.visual-poetry')
@endsection
