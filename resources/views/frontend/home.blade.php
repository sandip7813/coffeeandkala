@extends('layouts.app')

@section('title', 'COFFEE & KALA — Editorial Journal')

@section('content')
    @include('frontend.partials.home.hero')
    @include('frontend.partials.home.giant-photo')
    @include('frontend.partials.home.visual-storytelling')
    @include('frontend.partials.home.editorial')
    @include('frontend.partials.home.visual-poetry')
    @include('frontend.partials.home.journal-carousel')
@endsection
