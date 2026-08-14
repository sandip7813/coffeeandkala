@extends('layouts.error')

@section('title', 'Something Went Wrong — Coffee & Kala')

@section('content')
    @include('errors.partials.panel', [
        'code' => '500',
        'eyebrow' => 'A spill in the kitchen',
        'heading' => 'Something went wrong behind the counter',
        'copy' => "We've knocked something over on our end. Our team has been notified — please try again in a little while.",
    ])
@endsection
