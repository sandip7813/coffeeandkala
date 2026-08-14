@extends('adminlte::page')

@section('title', __('Edit Category'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('Edit Category') }}</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-adminlte-card icon="bi bi-tags" title="{{ $category->title }}">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                    @csrf
                    @method('PUT')

                    <x-adminlte-input name="title" label="{{ __('adminlte.name') }} *" :value="old('title', $category->title)" required />
                    <x-adminlte-input name="slug" label="{{ __('Slug') }} *" :value="old('slug', $category->slug)" pattern="[a-z0-9]+(-[a-z0-9]+)*" title="{{ __('Lowercase letters, numbers, and single hyphens between words (e.g. art-culture).') }}" required />
                    <x-adminlte-textarea name="description" label="{{ __('Description') }}" rows="3">{{ old('description', $category->description) }}</x-adminlte-textarea>
                    <x-adminlte-input type="number" name="sort_order" label="{{ __('Sort') }} *" :value="old('sort_order', $category->sort_order)" min="0" required />
                    <x-adminlte-input-switch name="status" label="{{ __('Active') }} *" value="1" :checked="old('status', $category->status)" />

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                        </button>
                    </div>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
