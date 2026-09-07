@extends('adminlte::page')

@section('title', __('Edit Blog'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('Edit Blog') }}</h3>
@stop

@section('content')
    @include('admin.articles._form', ['type' => 'journals'])
@stop

@push('js')
    @vite('resources/js/admin-articles.js')
@endpush
