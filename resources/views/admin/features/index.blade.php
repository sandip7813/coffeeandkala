@extends('adminlte::page')

@section('title', __('Features'))

@section('content_header')
    @include('admin.articles._index_header', ['type' => 'features', 'hasActiveFilters' => $hasActiveFilters])
@stop

@section('content')
    @include('admin.articles._index', ['type' => 'features', 'icon' => 'bi bi-journal-richtext'])
@stop

@push('js')
    @vite('resources/js/admin-home-section-toggles.js')
@endpush
