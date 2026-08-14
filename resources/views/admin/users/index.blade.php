@extends('adminlte::page')

@section('title', __('adminlte.users'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('adminlte.users') }}</h1>
        </div>
        <div class="col-sm-6 d-flex justify-content-sm-end">
            <button type="button" class="btn btn-outline-info" data-search-toggle="#userSearch" aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}" aria-controls="userSearch">
                <i class="bi bi-search me-1" aria-hidden="true"></i> {{ __('Search') }}
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="collapse {{ $hasActiveFilters ? 'show' : '' }}" id="userSearch">
        <x-adminlte-card class="mb-3">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="filter-name" class="form-label">{{ __('adminlte.name') }}</label>
                    <select id="filter-name" name="name" class="form-control"
                            data-select2-search
                            data-select2-url="{{ route('admin.users.search') }}"
                            data-placeholder="{{ __('Type at least 3 characters…') }}">
                        @if (filled($filters['name'] ?? null))
                            <option value="{{ $filters['name'] }}" selected>{{ $nameFilterLabel }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-role" class="form-label">{{ __('adminlte.roles') }}</label>
                    <select id="filter-role" name="role" class="form-select">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected(($filters['role'] ?? '') === $role->name)>{{ $role->label ?? $role->name }}</option>
                        @endforeach
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
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i> {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </x-adminlte-card>
    </div>

    <x-adminlte-card icon="bi bi-people" title="{{ __('adminlte.users') }}" bodyClass="p-0">
        <x-slot name="tools">
            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('adminlte.new_user') }}
            </a>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-striped align-middle m-0">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>{{ __('adminlte.name') }}</th>
                        <th>{{ __('adminlte.email') }}</th>
                        <th>Phone</th>
                        <th>{{ __('adminlte.roles') }}</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 4.5rem;">{{ __('adminlte.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                @if ($user->profile_photo_thumbnail_url)
                                    <img src="{{ $user->profile_photo_thumbnail_url }}" alt="{{ $user->full_name }}"
                                         class="rounded-circle" width="36" height="36" style="object-fit: cover;">
                                @else
                                    <i class="bi bi-person-circle text-body-secondary fs-3" aria-label="No profile picture" aria-hidden="true"></i>
                                @endif
                            </td>
                            <td><strong>{{ $user->full_name }}</strong></td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td class="text-muted">{{ $user->phone ?: '—' }}</td>
                            <td>
                                @forelse ($user->roles as $role)
                                    <span class="badge bg-primary">{{ $role->label ?? $role->name }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <x-admin.row-actions>
                                    @if (\Illuminate\Support\Facades\Route::has('adminlte.impersonate.start') && ! $user->is(auth()->user()))
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                               href="{{ route('admin.impersonate.start', $user) }}">
                                                <i class="bi bi-incognito" aria-hidden="true"></i>
                                                <span>{{ __('adminlte.login_as') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                           href="{{ route('admin.users.edit', $user) }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                            <span>{{ __('adminlte.edit') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                           data-bs-toggle="modal" data-bs-target="#userPhotoModal{{ $user->id }}">
                                            <i class="bi bi-image" aria-hidden="true"></i>
                                            <span>Profile Picture</span>
                                        </a>
                                    </li>
                                    @if (auth()->user()?->isSuperAdmin() && ! $user->is(auth()->user()))
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  data-confirm-delete
                                                  data-confirm-title="Delete this user?"
                                                  data-confirm-text="{{ $user->full_name }} will be permanently removed. This cannot be undone."
                                                  data-confirm-button="Yes, delete user"
                                                  data-cancel-button="{{ __('adminlte.cancel') }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="dropdown-item d-flex align-items-center gap-2 text-danger">
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
                        <tr><td colspan="7" class="text-center text-muted py-4">{{ __('adminlte.no_users') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot name="footer">
            {{ $users->links() }}
        </x-slot>
    </x-adminlte-card>

    @foreach ($users as $user)
        <div class="modal fade" id="userPhotoModal{{ $user->id }}" tabindex="-1"
             aria-labelledby="userPhotoModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.users.photo.update', $user) }}" enctype="multipart/form-data"
                          data-page-loading="Uploading picture…">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_reopen_modal" value="userPhotoModal{{ $user->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userPhotoModalLabel{{ $user->id }}">
                                {{ $user->full_name }} — Profile Picture
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            @if ($user->profile_photo_thumbnail_url)
                                <img src="{{ $user->profile_photo_thumbnail_url }}" alt="{{ $user->full_name }}"
                                     class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                            @else
                                <i class="bi bi-person-circle text-body-secondary d-block mb-3" style="font-size: 120px; line-height: 1;" aria-hidden="true"></i>
                            @endif

                            <x-adminlte-input-file name="profile_photo" label="Change picture" accept="image/*" />
                            <p class="form-text text-start mb-0">
                                Accepted formats: {{ strtoupper(implode(', ', config('media.profile_photo.formats'))) }}.
                                Max size: {{ number_format(config('media.profile_photo.max_size_kb') / 1024, 1) }} MB.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-1" aria-hidden="true"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@stop
