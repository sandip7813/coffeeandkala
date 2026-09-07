@extends('adminlte::page')

@section('title', __('Edit Poem'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('Edit Poem') }}</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <x-adminlte-card icon="bi bi-journal-text" :title="__('Edit Poem')">
                <div class="text-center mb-3">
                    @if ($poem->featuredImage)
                        <a href="{{ $poem->featuredImage->large_url }}" data-fancybox="poetry-edit" data-caption="{{ $poem->title }}">
                            <img src="{{ $poem->featuredImage->thumbnail_url }}" alt="{{ $poem->title }}" class="rounded" width="200" height="200" style="object-fit: cover; cursor: zoom-in;">
                        </a>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.poetry.update', $poem) }}" enctype="multipart/form-data" data-page-loading="{{ __('Saving…') }}">
                    @csrf
                    @method('PUT')

                    <x-adminlte-input name="title" label="{{ __('Title') }} *" maxlength="255" required :value="old('title', $poem->title)" />

                    <x-adminlte-input-file name="featured_image" label="{{ __('Featured Image') }}" accept="image/*" />
                    <p class="form-text mb-3">
                        {{ __('Accepted formats:') }} {{ strtoupper(implode(', ', config('media.poetry.formats'))) }}.
                        {{ __('Max size:') }} {{ number_format(config('media.poetry.max_size_kb') / 1024, 1) }} MB.
                        {{ __('Leave empty to keep the current image.') }}
                    </p>

                    <x-adminlte-textarea name="body" label="{{ __('Poetry') }} *" rows="10" required>{{ old('body', $poem->body) }}</x-adminlte-textarea>

                    <hr>
                    <h6 class="text-muted mb-3">{{ __('SEO Meta Data') }}</h6>
                    <x-adminlte-input name="meta_title" label="{{ __('Meta Title') }}" :value="old('meta_title', $meta->title)" />
                    <x-adminlte-textarea name="meta_description" label="{{ __('Meta Description') }}" rows="3">{{ old('meta_description', $meta->description) }}</x-adminlte-textarea>
                    <x-adminlte-input name="meta_keywords" label="{{ __('Meta Keywords') }}" :value="old('meta_keywords', $meta->keywords)" />

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.poetry.index') }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                        </button>
                    </div>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
