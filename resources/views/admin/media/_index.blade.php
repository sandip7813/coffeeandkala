{{-- Shared Gallery/Studio media grid. Expects: $media, $filters, $hasActiveFilters, $type ('gallery'|'studio'), $icon. --}}
@php
    $user = auth()->user();
    $canEdit = $user?->can("edit-{$type}");
    $canDelete = $user?->can("delete-{$type}");
    $canChangeStatus = $user?->can("change-{$type}-status");
    $canApprove = $user?->can("approve-{$type}");
@endphp

<div class="collapse {{ $hasActiveFilters ? 'show' : '' }}" id="{{ $type }}Search">
    <x-adminlte-card class="mb-3">
        <form method="GET" action="{{ route("admin.{$type}.index") }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="filter-{{ $type }}-title" class="form-label">{{ __('adminlte.name') }}</label>
                <input type="text" id="filter-{{ $type }}-title" name="title" class="form-control" placeholder="{{ __('Search by title') }}" value="{{ $filters['title'] ?? '' }}">
            </div>
            <div class="col-md-4">
                <label for="filter-{{ $type }}-status" class="form-label">{{ __('adminlte.status') }}</label>
                <select id="filter-{{ $type }}-status" name="status" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>{{ __('Pending') }}</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>{{ __('Active') }}</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1" aria-hidden="true"></i> {{ __('Search') }}
                </button>
                <a href="{{ route("admin.{$type}.index") }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i> {{ __('Reset') }}
                </a>
            </div>
        </form>
    </x-adminlte-card>
</div>

<x-adminlte-card :icon="$icon" :title="__(ucfirst($type))" bodyClass="p-3">
    <div class="row g-3">
        @forelse ($media as $item)
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="card h-100 shadow-sm">
                    <a href="{{ $item->large_url }}" data-fancybox="{{ $type }}" data-caption="{{ $item->title }}">
                        <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" class="card-img-top" style="aspect-ratio: 1 / 1; object-fit: cover; max-height: 140px;">
                    </a>
                    <div class="card-body p-2">
                        <p class="mb-1 fw-semibold text-truncate" title="{{ $item->title }}">{{ $item->title }}</p>
                        <p class="mb-2 small text-muted text-truncate" title="{{ $item->caption }}">{{ $item->caption }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            @if ($item->status === 'active')
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @elseif ($item->status === 'pending')
                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                            @endif

                            <x-admin.row-actions>
                                @if ($canEdit)
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route("admin.{$type}.edit", $item) }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                            <span>{{ __('adminlte.edit') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if ($canApprove && $item->status === 'pending')
                                    <li>
                                        <form method="POST" action="{{ route("admin.{$type}.approve", $item) }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                <i class="bi bi-check-circle" aria-hidden="true"></i>
                                                <span>{{ __('Approve') }}</span>
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                @if ($canChangeStatus && $item->status !== 'pending')
                                    <li>
                                        <form method="POST" action="{{ route("admin.{$type}.status.update", $item) }}"
                                              data-confirm-toggle
                                              data-confirm-title="{{ $item->status === 'active' ? __('Deactivate this image?') : __('Activate this image?') }}"
                                              data-confirm-text="{{ $item->title }} will be marked as {{ $item->status === 'active' ? __('inactive') : __('active') }}."
                                              data-confirm-button="{{ __('Yes, change status') }}"
                                              data-cancel-button="{{ __('adminlte.cancel') }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                <i class="bi bi-toggle2-on" aria-hidden="true"></i>
                                                <span>{{ __('Change status') }}</span>
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                @if ($canDelete)
                                    <li>
                                        <form method="POST" action="{{ route("admin.{$type}.destroy", $item) }}"
                                              data-confirm-delete
                                              data-confirm-title="{{ __('Delete this image?') }}"
                                              data-confirm-text="{{ $item->title }} {{ __('will be permanently deleted.') }}"
                                              data-confirm-button="{{ __('adminlte.delete') }}"
                                              data-cancel-button="{{ __('adminlte.cancel') }}"
                                              data-loading-text="{{ __('Deleting image…') }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                                <span>{{ __('adminlte.delete') }}</span>
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </x-admin.row-actions>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-4">{{ __('No images found.') }}</div>
        @endforelse
    </div>
    @if ($media->hasPages())
        <div class="pt-3">{{ $media->links() }}</div>
    @endif
</x-adminlte-card>
