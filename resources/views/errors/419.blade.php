@extends('layouts.error')

@section('title', 'Session Expired — Coffee & Kala')

@section('content')
    @include('errors.partials.panel', [
        'code' => '419',
        'eyebrow' => 'The page has gone cold',
        'heading' => 'Your session ran out of ink',
        'copy' => "This page sat open a little too long and expired for your safety. Head back and give it another go — freshly poured.",
    ])
@endsection
