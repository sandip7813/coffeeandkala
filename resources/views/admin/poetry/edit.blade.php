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

    <form method="POST" action="{{ route('admin.poetry.update', $poem) }}" enctype="multipart/form-data" data-page-loading="{{ __('Saving…') }}">
        @csrf
        @method('PUT')

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-content">
                <x-adminlte-input name="title" label="{{ __('Title') }} *" maxlength="255" required :value="new \Illuminate\Support\HtmlString(old('title', $poem->title))" />

                <div class="row align-items-start">
                    @if ($poem->featuredImage)
                        <div class="col-md text-center">
                            <a href="{{ $poem->featuredImage->large_url }}" data-fancybox="poetry-edit" data-caption="{{ $poem->title }}">
                                <img src="{{ $poem->featuredImage->thumbnail_url }}" alt="{{ $poem->title }}" class="rounded" width="100" height="100" style="object-fit: cover; cursor: zoom-in;">
                            </a>
                        </div>
                    @endif
                    <div class="col-md-11">
                        <x-adminlte-input-file name="featured_image" label="{{ __('Featured Image') }}" accept="image/*" />
                        <p class="form-text mb-3">
                            {{ __('Accepted formats:') }} {{ strtoupper(implode(', ', config('media.poetry.formats'))) }}.
                            {{ __('Max size:') }} {{ number_format(config('media.poetry.max_size_kb') / 1024, 1) }} MB.
                            {{ __('Leave empty to keep the current image.') }}
                        </p>
                    </div>
                </div>

                <x-adminlte-textarea name="body" label="{{ __('Poetry') }} *" rows="14" required>{{ old('body', $poem->body) }}</x-adminlte-textarea>
            </div>

            <div class="tab-pane fade" id="tab-seo">
                <x-adminlte-input name="meta_title" label="{{ __('Meta Title') }}" :value="new \Illuminate\Support\HtmlString(old('meta_title', $meta->title))" />
                <x-adminlte-textarea name="meta_description" label="{{ __('Meta Description') }}" rows="3">{{ old('meta_description', $meta->description) }}</x-adminlte-textarea>
                <x-adminlte-input name="meta_keywords" label="{{ __('Meta Keywords') }}" :value="new \Illuminate\Support\HtmlString(old('meta_keywords', $meta->keywords))" />
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.poetry.index') }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
            </button>
        </div>
    </form>
@stop
