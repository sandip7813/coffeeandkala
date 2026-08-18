@extends('adminlte::page')

@section('title', __('Upload Image'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('Upload Studio Image') }}</h3>
@stop

@section('content')
    @include('admin.media._create', ['type' => 'studio', 'icon' => 'bi bi-easel'])
@stop
