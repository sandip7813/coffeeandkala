@extends('adminlte::page')

@section('title', __('Journals'))

@section('content_header')
    @include('admin.articles._index_header', ['type' => 'journals', 'hasActiveFilters' => $hasActiveFilters])
@stop

@section('content')
    @include('admin.articles._index', ['type' => 'journals', 'icon' => 'bi bi-journal-text'])
@stop

@push('js')
    @vite('resources/js/admin-home-section-toggles.js')
@endpush
