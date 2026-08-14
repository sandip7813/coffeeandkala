@extends('layouts.error')

@section('title', 'Access Denied — Coffee & Kala')

@section('content')
    @include('errors.partials.panel', [
        'code' => '403',
        'eyebrow' => 'A locked door',
        'heading' => 'This corner is not open to you',
        'copy' => "Some pages are kept for private reading. If you believe this is a mistake, sign in with the right account or head back to something open to everyone.",
    ])
@endsection
