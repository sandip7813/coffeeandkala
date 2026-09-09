@extends('adminlte::page')

@section('title', 'Home Page')

@section('content_header')
    <h1 class="m-0">Home Page</h1>
    <p class="text-body-secondary mb-0">
        Choose which Feature/Journal articles appear in each homepage carousel, and drag to set their order.
        A section with nothing picked stays hidden on the homepage; a section with only one pick shows without
        slider arrows.
    </p>
@stop

@php
    $activeSection = session('home_section');
@endphp

@section('content')
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tab-home-sections">{{ __('Home Sections') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-seo">{{ __('SEO') }}</button>
        </li>
    </ul>

    <div class="tab-content">
    <div class="tab-pane fade show active" id="tab-home-sections">
    <div class="accordion home-sections-accordion" id="home-sections-accordion">
        @foreach ($sectionLabels as $section => $label)
            @php
                $picked = $sections[$section];
                $isOpen = $activeSection === $section;
            @endphp

            <div class="accordion-item home-section-card">
                <h2 class="accordion-header" id="home-section-{{ $section }}-heading">
                    <button
                        class="accordion-button {{ $isOpen ? '' : 'collapsed' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#home-section-{{ $section }}-collapse"
                        aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                        aria-controls="home-section-{{ $section }}-collapse"
                    >
                        <span class="flex-grow-1">{{ $label }}</span>
                        <span class="badge text-bg-secondary me-2">{{ count($picked) }} selected</span>
                    </button>
                </h2>
                <div id="home-section-{{ $section }}-collapse" class="accordion-collapse collapse {{ $isOpen ? 'show' : '' }}" aria-labelledby="home-section-{{ $section }}-heading">
                    <div class="accordion-body">
                        <form method="POST" action="{{ route('admin.home-sections.update', $section) }}" data-home-section>
                            @csrf
                            @method('PUT')

                            <div data-picked-inputs>
                                @foreach ($picked as $option)
                                    <input type="hidden" name="article_ids[]" value="{{ $option['id'] }}">
                                @endforeach
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    {{-- Search rather than a full list — with hundreds of
                                         articles, eagerly listing every one of them
                                         wouldn't scale. Nothing is fetched until the
                                         admin actually types a query. --}}
                                    <p class="fw-semibold mb-2">Add an article</p>
                                    <div class="d-flex gap-2">
                                        <select
                                            class="form-control home-section-add-select"
                                            data-select2-search
                                            data-select2-url="{{ route('admin.home-sections.search-articles', ['section' => $section]) }}"
                                            data-placeholder="Type at least 3 characters…"
                                        ></select>
                                        <button type="button" class="btn btn-outline-primary flex-shrink-0" data-add-article>Add</button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <p class="fw-semibold mb-2">Selected (drag to reorder)</p>
                                    <ul class="home-section-list home-section-list--picked" data-picked-list>
                                        @forelse ($picked as $option)
                                            <li class="home-section-article" data-article-id="{{ $option['id'] }}">
                                                <span class="home-section-article-label">
                                                    <i class="bi bi-grip-vertical text-body-secondary me-1" aria-hidden="true"></i>
                                                    {{ $option['label'] }}
                                                </span>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-remove-article>Remove</button>
                                            </li>
                                        @empty
                                            <li class="home-section-list-empty text-body-secondary" data-empty-hint>Nothing picked yet — this section is hidden on the homepage.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Save {{ $label }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    </div>

    <div class="tab-pane fade" id="tab-seo">
        <x-adminlte-card>
            <form method="POST" action="{{ route('admin.home-sections.meta.update') }}" data-page-loading="{{ __('Saving SEO…') }}">
                @csrf
                @method('PUT')

                <x-adminlte-input name="meta_title" label="{{ __('Meta Title') }}" :value="old('meta_title', $meta->title)" />
                <x-adminlte-textarea name="meta_description" label="{{ __('Meta Description') }}" rows="3">{{ old('meta_description', $meta->description) }}</x-adminlte-textarea>
                <x-adminlte-input name="meta_keywords" label="{{ __('Meta Keywords') }}" :value="old('meta_keywords', $meta->keywords)" />

                <button type="submit" class="btn btn-primary mt-3">{{ __('Save SEO') }}</button>
            </form>
        </x-adminlte-card>
    </div>
    </div>
@stop

@section('css')
    <style>
        /* Small gap between accordion items — since they're no longer
           flush against each other, each one needs its own top border
           and full corner rounding instead of only the first/last item
           getting it (Bootstrap's default assumes a flush stack). */
        .home-sections-accordion .accordion-item {
            margin-bottom: 1rem;
            border-top-width: var(--bs-accordion-border-width) !important;
            border-radius: var(--bs-accordion-border-radius) !important;
        }

        .home-sections-accordion .accordion-item:last-child {
            margin-bottom: 0;
        }

        .home-sections-accordion .accordion-item .accordion-button {
            border-radius: var(--bs-accordion-inner-border-radius) var(--bs-accordion-inner-border-radius) 0 0 !important;
        }

        .home-sections-accordion .accordion-item .accordion-collapse {
            border-radius: 0 0 var(--bs-accordion-border-radius) var(--bs-accordion-border-radius);
        }

        .home-section-list {
            list-style: none;
            margin: 0;
            padding: 0;
            min-height: 48px;
            border: 1px solid var(--bs-border-color);
            border-radius: 0.375rem;
            max-height: 360px;
            overflow-y: auto;
        }

        .home-section-article {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 12px;
            border-bottom: 1px solid var(--bs-border-color);
            cursor: grab;
        }

        .home-section-article:last-child {
            border-bottom: 0;
        }

        .home-section-article-label {
            font-size: 0.9rem;
        }

        .home-section-list-empty {
            padding: 12px;
            font-size: 0.85rem;
            cursor: default;
        }
    </style>
@stop

@section('js')
    @vite('resources/js/admin-home-sections.js')
@stop
