@extends('adminlte::page')

@section('title', __('Add Poem'))

@section('content_header')
    <h3 class="mb-0">{{ __('Add Poem') }}</h3>
@stop

@section('content')
    <form method="POST" action="{{ route('admin.poetry.store') }}" enctype="multipart/form-data"
          data-page-loading="{{ __('Saving poem…') }}">
        @csrf

        <x-adminlte-input name="title" label="{{ __('Title') }} *" maxlength="255" required :value="new \Illuminate\Support\HtmlString(old('title'))" />

        <x-adminlte-input-file name="featured_image" label="{{ __('Featured Image') }} *" accept="image/*" required />
        <p class="form-text mb-3">
            {{ __('Accepted formats:') }} {{ strtoupper(implode(', ', config('media.poetry.formats'))) }}.
            {{ __('Max size:') }} {{ number_format(config('media.poetry.max_size_kb') / 1024, 1) }} MB.
        </p>

        <x-adminlte-textarea name="body" label="{{ __('Poetry') }} *" rows="14" required>{{ old('body') }}</x-adminlte-textarea>

        @unless (auth()->user()?->can('approve-poetry'))
            <div class="alert alert-info">
                {{ __('Your poem will be marked as pending until a super admin approves it.') }}
            </div>
        @endunless

        <div class="d-flex gap-2">
            <a href="{{ route('admin.poetry.index') }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-upload me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
            </button>
        </div>
    </form>
@stop
