@extends('layouts.error')

@section('title', 'Page Not Found — Coffee & Kala')

@section('content')
    @include('errors.partials.panel', [
        'code' => '404',
        'eyebrow' => 'Lost between the pages',
        'heading' => "This chapter hasn't been written",
        'copy' => "The page you're looking for has wandered off, or never existed at all. Let's find your way back to a story that's actually on the shelf.",
    ])
@endsection
