@extends('adminlte::page')

@section('title', __('Gallery'))

@section('content_header')
    @include('admin.media._index_header', ['type' => 'gallery', 'hasActiveFilters' => $hasActiveFilters])
@stop

@section('content')
    @include('admin.media._index', ['type' => 'gallery', 'icon' => 'bi bi-images'])
@stop
