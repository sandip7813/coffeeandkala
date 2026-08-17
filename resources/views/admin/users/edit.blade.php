@extends('adminlte::page')

@section('title', __('adminlte.edit_user'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('adminlte.edit_user') }}</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-adminlte-card icon="bi bi-person-gear" title="{{ __('adminlte.edit_user') }}" class="mb-4">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" data-page-loading="Saving user…">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-input name="first_name" label="First name" :value="$user->first_name" required />
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-input name="last_name" label="Last name" :value="$user->last_name" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-input name="email" type="email" label="{{ __('adminlte.email') }}" :value="$user->email" required />
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-input name="phone" type="tel" label="Phone number" :value="$user->phone"
                                              maxlength="10" inputmode="numeric" pattern="[0-9]{1,10}"
                                              placeholder="10 digits max" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('adminlte.roles') }}</label>
                        @if ($user->isSuperAdmin())
                            <div>
                                <span class="badge bg-dark">Super Admin</span>
                                <div class="form-text">The Super Admin role is seeded and cannot be assigned or changed here.</div>
                            </div>
                        @else
                            @error('role')
                                <div class="text-danger small mb-1">{{ $message }}</div>
                            @enderror
                            @php
                                $selectedRoleId = old('role', $user->roles->first()?->id);
                            @endphp
                            <div class="d-flex flex-wrap gap-3">
                                @forelse ($roles as $role)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="role"
                                               value="{{ $role->id }}" id="role-{{ $role->id }}"
                                               @checked((string) $selectedRoleId === (string) $role->id)>
                                        <label class="form-check-label" for="role-{{ $role->id }}">
                                            {{ $role->label ?? $role->name }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">{{ __('adminlte.no_roles') }}</p>
                                @endforelse
                            </div>
                        @endif
                    </div>

                    @if (auth()->user()?->isSuperAdmin())
                        <div class="mb-3">
                            <label class="form-label d-block">{{ __('adminlte.status') }}</label>
                            @switch($user->status)
                                @case('pending')
                                    <span class="badge text-bg-warning">Pending</span>
                                    @break
                                @case('inactive')
                                    <span class="badge text-bg-secondary">Inactive</span>
                                    @break
                                @default
                                    <span class="badge text-bg-success">Active</span>
                            @endswitch
                            @unless ($user->is(auth()->user()))
                                <p class="form-text mb-0">
                                    Use the {{ $user->is_active ? 'Deactivate' : 'Activate' }} action from the users list to change this.
                                </p>
                            @endunless
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                        </button>
                    </div>
                </form>
            </x-adminlte-card>

            <x-adminlte-card icon="bi bi-image" title="Profile picture">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if ($user->profile_photo_thumbnail_url)
                        <img src="{{ $user->profile_photo_thumbnail_url }}" alt="{{ $user->full_name }}"
                             class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                    @else
                        <i class="bi bi-person-circle text-body-secondary" style="font-size: 80px; line-height: 1;" aria-hidden="true"></i>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.users.photo.update', $user) }}" enctype="multipart/form-data"
                      data-page-loading="Uploading picture…">
                    @csrf
                    @method('PUT')
                    <x-adminlte-input-file name="profile_photo" label="Upload picture" accept="image/*" />
                    <p class="form-text">
                        Accepted formats: {{ strtoupper(implode(', ', config('media.profile_photo.formats'))) }}.
                        Max size: {{ number_format(config('media.profile_photo.max_size_kb') / 1024, 1) }} MB.
                    </p>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-upload me-1" aria-hidden="true"></i> Upload
                    </button>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
