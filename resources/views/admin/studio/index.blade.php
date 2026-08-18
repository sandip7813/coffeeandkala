@extends('adminlte::page')

@section('title', __('Studio'))

@section('content_header')
    @include('admin.media._index_header', ['type' => 'studio', 'hasActiveFilters' => $hasActiveFilters])
@stop

@section('content')
    @include('admin.media._index', ['type' => 'studio', 'icon' => 'bi bi-easel'])
@stop
