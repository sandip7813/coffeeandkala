@extends('adminlte::page')

@section('title', __('Our Story'))

@section('content_header')
    <h3 class="mb-0">{{ __('Our Story') }}</h3>
@stop

@section('content')
    <style>
        .article-block-enter { opacity: 0; transform: translateY(-12px); transition: opacity .25s ease, transform .25s ease; }
        .article-block-enter-active { opacity: 1; transform: none; }
        .article-section--inactive { background-color: rgba(108, 117, 125, .1); transition: background-color .2s ease; }
        .article-section--inactive > .card-body { opacity: .75; }
        .article-section-drag-handle { cursor: grab; }
        .article-section-drag-handle:active { cursor: grabbing; }
        .article-section-drag-handle [data-no-drag] { cursor: default; }
    </style>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tab-content">{{ __('Content') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-seo">{{ __('SEO') }}</button>
        </li>
    </ul>

    <div class="tab-content">
    <div class="tab-pane fade show active" id="tab-content">
    {{-- Content-only — no Essentials/FAQs/Page Sections; this is the
         singleton "Our Story" article, edited via AJAX just like the
         Features/Journals Content tab (see admin-articles.js). --}}
    <form method="POST" action="{{ route('admin.our-story.update') }}" enctype="multipart/form-data" id="article-form" data-ajax-form data-loading-text="{{ __('Saving…') }}">
        @csrf
        @method('PUT')

        <div id="article-sections" data-reorder-url="{{ route('admin.our-story.sections.reorder', $article) }}">
            @forelse ($article->sections as $index => $section)
                @include('admin.articles._section_fields', ['index' => $index, 'section' => $section, 'type' => $type, 'isEdit' => true, 'article' => $article])
            @empty
                @include('admin.articles._section_fields', ['index' => 0, 'section' => null, 'type' => $type, 'isEdit' => true, 'article' => $article])
            @endforelse
        </div>

        <button type="button" class="btn btn-outline-primary" id="add-section">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('Add Section') }}
        </button>

        <template id="section-template">
            @include('admin.articles._section_fields', ['index' => '__INDEX__', 'section' => null, 'type' => $type, 'isEdit' => true, 'article' => $article])
        </template>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
            </button>
        </div>
    </form>
    </div>

    <div class="tab-pane fade" id="tab-seo">
        <x-adminlte-card>
            <form method="POST" action="{{ route('admin.our-story.meta.update') }}" data-page-loading="{{ __('Saving SEO…') }}">
                @csrf
                @method('PUT')

                <x-adminlte-input name="meta_title" label="{{ __('Meta Title') }}" :value="old('meta_title', $meta->title)" />
                <x-adminlte-textarea name="meta_description" label="{{ __('Meta Description') }}" rows="3">{{ old('meta_description', $meta->description) }}</x-adminlte-textarea>
                <x-adminlte-input name="meta_keywords" label="{{ __('Meta Keywords') }}" :value="old('meta_keywords', $meta->keywords)" />

                <button type="submit" class="btn btn-primary">{{ __('Save SEO') }}</button>
            </form>
        </x-adminlte-card>
    </div>
    </div>
@stop

@push('js')
    @vite('resources/js/admin-articles.js')
@endpush
