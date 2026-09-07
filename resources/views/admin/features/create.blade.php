@extends('adminlte::page')

@section('title', __('New Feature Article'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('New Feature Article') }}</h3>
@stop

@section('content')
    @include('admin.articles._form', ['type' => 'features'])
@stop

@push('js')
    @vite('resources/js/admin-articles.js')
@endpush
