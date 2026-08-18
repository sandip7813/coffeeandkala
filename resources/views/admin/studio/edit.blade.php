@extends('adminlte::page')

@section('title', __('Edit Image'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('Edit Studio Image') }}</h3>
@stop

@section('content')
    @include('admin.media._edit', ['type' => 'studio', 'icon' => 'bi bi-easel', 'media' => $media])
@stop
