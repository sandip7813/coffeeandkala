@extends('adminlte::page')

@section('title', __('Edit Category'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('Edit Category') }}</h3>
@stop

@section('content')
    <x-adminlte-card icon="bi bi-tags" :title="$category->title">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tab-content">{{ __('Content') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-seo">{{ __('SEO') }}</button>
            </li>
        </ul>

        <form method="POST" action="{{ route('admin.categories.update', $category) }}" data-page-loading="{{ __('Saving…') }}">
            @csrf
            @method('PUT')

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-content">
                    <x-adminlte-input name="title" label="{{ __('adminlte.name') }} *" :value="new \Illuminate\Support\HtmlString(old('title', $category->title))" required />
                    <x-adminlte-input name="slug" label="{{ __('Slug') }} *" :value="new \Illuminate\Support\HtmlString(old('slug', $category->slug))" pattern="[a-z0-9]+(-[a-z0-9]+)*" title="{{ __('Lowercase letters, numbers, and single hyphens between words (e.g. art-culture).') }}" required />
                    <x-adminlte-textarea name="description" label="{{ __('Description') }}" rows="3">{{ old('description', $category->description) }}</x-adminlte-textarea>
                    <x-adminlte-input type="number" name="sort_order" label="{{ __('Sort') }} *" :value="old('sort_order', $category->sort_order)" min="0" required />
                    <x-adminlte-input-switch name="status" label="{{ __('Active') }} *" value="1" :checked="old('status', $category->status)" />
                </div>

                <div class="tab-pane fade" id="tab-seo">
                    <x-adminlte-input name="meta_title" label="{{ __('Meta Title') }}" :value="new \Illuminate\Support\HtmlString(old('meta_title', $meta->title))" />
                    <x-adminlte-textarea name="meta_description" label="{{ __('Meta Description') }}" rows="3">{{ old('meta_description', $meta->description) }}</x-adminlte-textarea>
                    <x-adminlte-input name="meta_keywords" label="{{ __('Meta Keywords') }}" :value="new \Illuminate\Support\HtmlString(old('meta_keywords', $meta->keywords))" />
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                </button>
            </div>
        </form>
    </x-adminlte-card>
@stop
