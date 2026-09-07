@extends('adminlte::page')

@section('title', __('Poetry'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('Poetry') }}</h1>
        </div>
        <div class="col-sm-6 text-sm-end">
            <button type="button" class="btn btn-sm btn-outline-info me-2" data-search-toggle="#poetrySearch" aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}" aria-controls="poetrySearch">
                <i class="bi bi-search me-1" aria-hidden="true"></i> {{ __('Search') }}
            </button>
            @if (auth()->user()?->can('upload-poetry'))
                <a href="{{ route('admin.poetry.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('Add Poem') }}
                </a>
            @endif
        </div>
    </div>
@stop

@php
    $user = auth()->user();
    $canEdit = $user?->can('edit-poetry');
    $canDelete = $user?->can('delete-poetry');
    $canChangeStatus = $user?->can('change-poetry-status');
    $canApprove = $user?->can('approve-poetry');
@endphp

@section('content')
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tab-poems">{{ __('Poems') }}</button>
        </li>
        @if (auth()->user()?->can('edit-poetry'))
            <li class="nav-item" role="presentation">
                <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-seo">{{ __('SEO') }}</button>
            </li>
        @endif
    </ul>

    <div class="tab-content">
    <div class="tab-pane fade show active" id="tab-poems">
    <div class="collapse {{ $hasActiveFilters ? 'show' : '' }}" id="poetrySearch">
        <x-adminlte-card class="mb-3">
            <form method="GET" action="{{ route('admin.poetry.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="filter-poetry-title" class="form-label">{{ __('adminlte.name') }}</label>
                    <input type="text" id="filter-poetry-title" name="title" class="form-control" placeholder="{{ __('Search by title') }}" value="{{ $filters['title'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="filter-poetry-status" class="form-label">{{ __('adminlte.status') }}</label>
                    <select id="filter-poetry-status" name="status" class="form-select">
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
                    <a href="{{ route('admin.poetry.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i> {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </x-adminlte-card>
    </div>

    <x-adminlte-card icon="bi bi-journal-text" :title="__('Poetry')" bodyClass="p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>{{ __('adminlte.name') }}</th>
                        <th>{{ __('Created By') }}</th>
                        <th>{{ __('adminlte.status') }}</th>
                        <th class="text-end">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($poems as $poem)
                        <tr>
                            <td>
                                @if ($poem->featuredImage)
                                    <a href="{{ $poem->featuredImage->large_url }}" data-fancybox="poetry" data-caption="{{ $poem->title }}">
                                        <img src="{{ $poem->featuredImage->thumbnail_url }}" alt="" class="rounded border" style="height:48px; width:48px; object-fit:cover; cursor:zoom-in;">
                                    </a>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $poem->title }}</td>
                            <td>
                                {{ $poem->creator?->name ?? __('Unknown') }}
                                @if ($poem->approver)
                                    <div class="small text-muted">{{ __('Approved by') }} {{ $poem->approver->name }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($poem->status === 'active')
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @elseif ($poem->status === 'pending')
                                    <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <x-admin.row-actions>
                                    @if ($canEdit)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.poetry.edit', $poem) }}">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                                <span>{{ __('adminlte.edit') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($canApprove && $poem->status === 'pending')
                                        <li>
                                            <form method="POST" action="{{ route('admin.poetry.approve', $poem) }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                    <i class="bi bi-check-circle" aria-hidden="true"></i>
                                                    <span>{{ __('Approve') }}</span>
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                    @if ($canChangeStatus && $poem->status !== 'pending')
                                        <li>
                                            <form method="POST" action="{{ route('admin.poetry.status.update', $poem) }}"
                                                  data-confirm-toggle
                                                  data-confirm-title="{{ $poem->status === 'active' ? __('Deactivate this poem?') : __('Activate this poem?') }}"
                                                  data-confirm-text="{{ $poem->title }} will be marked as {{ $poem->status === 'active' ? __('inactive') : __('active') }}."
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
                                            <form method="POST" action="{{ route('admin.poetry.destroy', $poem) }}"
                                                  data-confirm-delete
                                                  data-confirm-title="{{ __('Delete this poem?') }}"
                                                  data-confirm-text="{{ $poem->title }} {{ __('will be permanently deleted.') }}"
                                                  data-confirm-button="{{ __('adminlte.delete') }}"
                                                  data-cancel-button="{{ __('adminlte.cancel') }}"
                                                  data-loading-text="{{ __('Deleting poem…') }}">
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">{{ __('No poems found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($poems->hasPages())
            <div class="p-3">{{ $poems->links() }}</div>
        @endif
    </x-adminlte-card>
    </div>

    @if (auth()->user()?->can('edit-poetry'))
        <div class="tab-pane fade" id="tab-seo">
            <x-adminlte-card>
                <form method="POST" action="{{ route('admin.poetry.meta.update') }}" data-page-loading="{{ __('Saving SEO…') }}">
                    @csrf
                    @method('PUT')

                    <x-adminlte-input name="meta_title" label="{{ __('Meta Title') }}" :value="old('meta_title', $meta->title)" />
                    <x-adminlte-textarea name="meta_description" label="{{ __('Meta Description') }}" rows="3">{{ old('meta_description', $meta->description) }}</x-adminlte-textarea>
                    <x-adminlte-input name="meta_keywords" label="{{ __('Meta Keywords') }}" :value="old('meta_keywords', $meta->keywords)" />

                    <button type="submit" class="btn btn-primary mt-3">{{ __('Save SEO') }}</button>
                </form>
            </x-adminlte-card>
        </div>
    @endif
    </div>
@stop
