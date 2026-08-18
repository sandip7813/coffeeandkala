@extends('adminlte::page')

@section('title', __('Edit Image'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('Edit Gallery Image') }}</h3>
@stop

@section('content')
    @include('admin.media._edit', ['type' => 'gallery', 'icon' => 'bi bi-images', 'media' => $media])
@stop
