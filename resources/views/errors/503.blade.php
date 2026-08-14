@extends('layouts.error')

@section('title', 'Down for Maintenance — Coffee & Kala')

@section('content')
    @include('errors.partials.panel', [
        'code' => '503',
        'eyebrow' => 'Closed for a moment',
        'heading' => "We're tidying up the studio",
        'copy' => "Coffee & Kala is briefly offline for maintenance. We'll have the doors open again shortly — thank you for your patience.",
    ])
@endsection
