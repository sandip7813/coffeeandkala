@extends('adminlte::page')

@section('title', __('Upload Image'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('Upload Gallery Image') }}</h3>
@stop

@section('content')
    @include('admin.media._create', ['type' => 'gallery', 'icon' => 'bi bi-images'])
@stop
