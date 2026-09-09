@extends('adminlte::page')

@section('title', 'Meta Data')

@section('content_header')
    <h1 class="m-0">Meta Data</h1>
    <p class="text-body-secondary mb-0">
        Set the meta title, description, and keywords search engines see for each of the site's main pages.
        Every Feature/Journal article and Poem is also editable from here, alongside its own edit screen.
    </p>
@stop

@section('content')
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tab-main">{{ __('Main Pages') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-features">{{ __('Features') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-journals">{{ __('Journal') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-poetry">{{ __('Poetry') }}</button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Main Pages: every static page's own meta, including the
             Features/Journal/Poetry index pages — those tabs otherwise
             hold only their content listing below. --}}
        <div class="tab-pane fade show active" id="tab-main">
            <div class="accordion meta-accordion" id="meta-main-accordion">
                @foreach ($mainMetas as $key => $meta)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="meta-{{ $key }}-heading">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#meta-{{ $key }}-collapse"
                                aria-expanded="false"
                                aria-controls="meta-{{ $key }}-collapse"
                            >
                                {{ \App\Models\Meta::STATIC_PAGES[$key] }}
                            </button>
                        </h2>
                        <div id="meta-{{ $key }}-collapse" class="accordion-collapse collapse" aria-labelledby="meta-{{ $key }}-heading" data-bs-parent="#meta-main-accordion">
                            <div class="accordion-body">
                                @include('admin.meta._page-form', ['key' => $key, 'meta' => $meta])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Features/Journal/Poetry: their own category pages' and content
             pages' meta — 10 at a time, paged in via fetch (see
             resources/js/admin-meta.js) rather than a full page reload. The
             index page's own meta lives under Main Pages above. --}}
        <div class="tab-pane fade" id="tab-features">
            <h6 class="text-muted mb-3">{{ __('Feature Categories') }}</h6>
            <div data-meta-list="feature-categories" data-list-url="{{ route('admin.meta.content.list', 'feature-categories') }}">{!! $featureCategoriesListHtml !!}</div>

            <h6 class="text-muted mb-3 mt-4">{{ __('Feature Articles') }}</h6>
            <div data-meta-list="features" data-list-url="{{ route('admin.meta.content.list', 'features') }}">{!! $featuresListHtml !!}</div>
        </div>

        <div class="tab-pane fade" id="tab-journals">
            <h6 class="text-muted mb-3">{{ __('Journal Categories') }}</h6>
            <div data-meta-list="journal-categories" data-list-url="{{ route('admin.meta.content.list', 'journal-categories') }}">{!! $journalCategoriesListHtml !!}</div>

            <h6 class="text-muted mb-3 mt-4">{{ __('Journal Articles') }}</h6>
            <div data-meta-list="journals" data-list-url="{{ route('admin.meta.content.list', 'journals') }}">{!! $journalsListHtml !!}</div>
        </div>

        <div class="tab-pane fade" id="tab-poetry">
            <div data-meta-list="poetry" data-list-url="{{ route('admin.meta.content.list', 'poetry') }}">{!! $poetryListHtml !!}</div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Small gap between accordion items — since they're no longer
           flush against each other, each one needs its own top border
           and full corner rounding instead of only the first/last item
           getting it (Bootstrap's default assumes a flush stack). */
        .meta-accordion .accordion-item {
            margin-bottom: 1rem;
            border-top-width: var(--bs-accordion-border-width) !important;
            border-radius: var(--bs-accordion-border-radius) !important;
        }

        .meta-accordion .accordion-item:last-child {
            margin-bottom: 0;
        }

        .meta-accordion .accordion-item .accordion-button {
            border-radius: var(--bs-accordion-inner-border-radius) var(--bs-accordion-inner-border-radius) 0 0 !important;
        }

        .meta-accordion .accordion-item .accordion-collapse {
            border-radius: 0 0 var(--bs-accordion-border-radius) var(--bs-accordion-border-radius);
        }
    </style>
@stop

@section('js')
    @vite('resources/js/admin-meta.js')
@stop
