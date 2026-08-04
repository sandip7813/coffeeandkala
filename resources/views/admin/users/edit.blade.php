@extends('adminlte::page')

@section('title', __('adminlte.edit_user'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('adminlte.edit_user') }}</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-adminlte-card icon="bi bi-person-gear" title="{{ __('adminlte.edit_user') }}">
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

                    @if (auth()->user()?->isSuperAdmin() && ! $user->is(auth()->user()))
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       name="is_active" value="1" id="is_active"
                                       @checked(old('is_active', $user->is_active))>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            @error('is_active')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
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
        </div>
    </div>
@stop
