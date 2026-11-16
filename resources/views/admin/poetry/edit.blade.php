@extends('adminlte::page')

@section('title', __('Edit Poem'))

@section('content_header')
    <h3 class="mb-0">{{ __('Edit Poem') }}</h3>
@stop

@section('content')
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tab-content">{{ __('Content') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-seo">{{ __('SEO') }}</button>
        </li>
    </ul>

    @include('admin.poetry._edit-form')
@stop
