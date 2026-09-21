@extends('adminlte::page')

@section('title', __('Closing One-Liners'))

@section('content_header')
    <h1 class="m-0">{{ __('Closing One-Liners') }}</h1>
    <p class="text-body-secondary mb-0">
        {{ __('The short closing line near the bottom of each page. Leave a field empty to hide it.') }}
    </p>
@stop

@section('content')
    <x-adminlte-card icon="bi bi-quote">
        <form method="POST" action="{{ route('admin.page-one-liners.update') }}" data-page-loading="{{ __('Saving…') }}">
            @csrf
            @method('PUT')

            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tab-main-pages">{{ __('Main Pages') }}</button>
                </li>
                @foreach ($categoryGroups as $group => $categories)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-{{ \Illuminate\Support\Str::slug($group) }}">{{ __($group) }}</button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-main-pages">
                    @foreach ($pages as $page => $label)
                        <x-adminlte-textarea name="lines[{{ $page }}]" label="{{ $label }}" rows="2" maxlength="150">{{ old('lines.'.$page, $lines[$page]) }}</x-adminlte-textarea>
                    @endforeach
                </div>

                @foreach ($categoryGroups as $group => $categories)
                    <div class="tab-pane fade" id="tab-{{ \Illuminate\Support\Str::slug($group) }}">
                        @forelse ($categories as $category)
                            <x-adminlte-textarea name="category_lines[{{ $category->id }}]" :label="$category->title" rows="2" maxlength="150">{{ old('category_lines.'.$category->id, $category->closing_line) }}</x-adminlte-textarea>
                        @empty
                            <p class="text-muted">{{ __('No categories yet.') }}</p>
                        @endforelse
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
            </button>
        </form>
    </x-adminlte-card>
@stop
