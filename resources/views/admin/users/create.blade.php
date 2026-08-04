@extends('adminlte::page')

@section('title', __('adminlte.new_user'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('adminlte.new_user') }}</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-adminlte-card icon="bi bi-person-plus" title="{{ __('adminlte.new_user') }}">
                <form method="POST" action="{{ route('admin.users.store') }}" data-page-loading="Creating user…">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-input name="first_name" label="First name" required />
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-input name="last_name" label="Last name" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-input name="email" type="email" label="{{ __('adminlte.email') }}" required />
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-input name="phone" type="tel" label="Phone number"
                                              maxlength="10" inputmode="numeric" pattern="[0-9]{1,10}"
                                              placeholder="10 digits max" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('adminlte.roles') }}</label>
                        @error('role')
                            <div class="text-danger small mb-1">{{ $message }}</div>
                        @enderror
                        <div class="d-flex flex-wrap gap-3">
                            @forelse ($roles as $role)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role"
                                           value="{{ $role->id }}" id="role-{{ $role->id }}"
                                           @checked((string) old('role') === (string) $role->id)>
                                    <label class="form-check-label" for="role-{{ $role->id }}">
                                        {{ $role->label ?? $role->name }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('adminlte.no_roles') }}</p>
                            @endforelse
                        </div>
                    </div>

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
