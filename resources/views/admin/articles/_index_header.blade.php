{{-- Shared Features/Journals list page header. Expects: $type ('features'|'journals'), $hasActiveFilters. --}}
@php
    $heading = $type === 'journals' ? __('Journals (Blogs)') : __('Features (Articles)');
    $newLabel = $type === 'journals' ? __('New Blog') : __('New Article');
@endphp
<div class="row">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $heading }}</h1>
    </div>
    <div class="col-sm-6 text-sm-end">
        <button type="button" class="btn btn-sm btn-outline-info me-2" data-search-toggle="#{{ $type }}Search" aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}" aria-controls="{{ $type }}Search">
            <i class="bi bi-search me-1" aria-hidden="true"></i> {{ __('Search') }}
        </button>
        @if (auth()->user()?->can("create-{$type}"))
            <a href="{{ route("admin.{$type}.create") }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ $newLabel }}
            </a>
        @endif
    </div>
</div>
