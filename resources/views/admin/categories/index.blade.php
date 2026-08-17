@extends('adminlte::page')

@section('title', __('Categories'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('Categories') }}</h1>
        </div>
        <div class="col-sm-6 text-sm-end">
            <button type="button" class="btn btn-sm btn-outline-info" data-search-toggle="#categorySearch" aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}" aria-controls="categorySearch">
                <i class="bi bi-search me-1" aria-hidden="true"></i> {{ __('Search') }}
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="collapse {{ $hasActiveFilters ? 'show' : '' }}" id="categorySearch">
        <x-adminlte-card class="mb-3">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="filter-title" class="form-label">{{ __('adminlte.name') }}</label>
                    <input type="text" id="filter-title" name="title" class="form-control" placeholder="{{ __('Search by title') }}" value="{{ $filters['title'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="filter-type" class="form-label">{{ __('Type') }}</label>
                    <select id="filter-type" name="type" class="form-select">
                        <option value="">{{ __('All') }}</option>
                        <option value="feature" @selected(($filters['type'] ?? '') === 'feature')>{{ __('Feature') }}</option>
                        <option value="journal" @selected(($filters['type'] ?? '') === 'journal')>{{ __('Journal') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-status" class="form-label">{{ __('adminlte.status') }}</label>
                    <select id="filter-status" name="status" class="form-select">
                        <option value="">{{ __('All') }}</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>{{ __('Active') }}</option>
                        <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1" aria-hidden="true"></i> {{ __('Search') }}
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i> {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </x-adminlte-card>
    </div>

    <x-adminlte-card icon="bi bi-tags" title="{{ __('Categories') }}" bodyClass="p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>{{ __('adminlte.name') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Sort') }}</th>
                        <th>{{ __('adminlte.status') }}</th>
                        <th class="text-end" style="width: 4.5rem;">{{ __('adminlte.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->title }}</td>
                            <td><span class="badge bg-info text-capitalize">{{ $category->type }}</span></td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                @if ($category->status)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <x-admin.row-actions>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                           href="{{ route('admin.categories.edit', $category) }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                            <span>{{ __('adminlte.edit') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.categories.status.update', $category) }}"
                                              data-confirm-toggle
                                              data-confirm-title="{{ $category->status ? __('Deactivate this category?') : __('Activate this category?') }}"
                                              data-confirm-text="{{ $category->title }} will be marked as {{ $category->status ? __('inactive') : __('active') }}."
                                              data-confirm-button="{{ __('Yes, change status') }}"
                                              data-cancel-button="{{ __('adminlte.cancel') }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                    class="dropdown-item d-flex align-items-center gap-2">
                                                <i class="bi bi-toggle2-on" aria-hidden="true"></i>
                                                <span>{{ __('Change status') }}</span>
                                            </button>
                                        </form>
                                    </li>
                                </x-admin.row-actions>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No categories found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($categories->hasPages())
            <div class="p-3">{{ $categories->links() }}</div>
        @endif
    </x-adminlte-card>
@stop
