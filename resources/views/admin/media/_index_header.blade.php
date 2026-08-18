{{-- Shared Gallery/Studio list page header. Expects: $type ('gallery'|'studio'), $hasActiveFilters. --}}
<div class="row">
    <div class="col-sm-6">
        <h1 class="m-0">{{ __(ucfirst($type)) }}</h1>
    </div>
    <div class="col-sm-6 text-sm-end">
        <button type="button" class="btn btn-sm btn-outline-info me-2" data-search-toggle="#{{ $type }}Search" aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}" aria-controls="{{ $type }}Search">
            <i class="bi bi-search me-1" aria-hidden="true"></i> {{ __('Search') }}
        </button>
        @if (auth()->user()?->can("upload-{$type}"))
            <a href="{{ route("admin.{$type}.create") }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('Upload Image') }}
            </a>
        @endif
    </div>
</div>
