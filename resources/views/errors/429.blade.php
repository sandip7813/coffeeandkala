@extends('layouts.error')

@section('title', 'Slow Down — Coffee & Kala')

@section('content')
    @include('errors.partials.panel', [
        'code' => '429',
        'eyebrow' => 'A gentle pause',
        'heading' => "Even the best cup needs a moment to brew",
        'copy' => "You've made a few too many requests in a hurry. Take a breath, and try again shortly.",
    ])
@endsection
