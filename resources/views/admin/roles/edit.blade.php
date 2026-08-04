@extends('adminlte::page')

@section('title', __('adminlte.edit_role'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('adminlte.edit_role') }}</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-adminlte-card icon="bi bi-shield-lock" title="{{ __('adminlte.edit_role') }}">
                <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                    @csrf
                    @method('PUT')

                    <x-adminlte-input name="name" label="{{ __('adminlte.name') }}" :value="$role->name" required />
                    <x-adminlte-input name="label" label="{{ __('adminlte.label') }}" :value="$role->label" />

                    <div class="mb-3">
                        <label class="form-label">{{ __('adminlte.permissions') }}</label>
                        @error('permissions')
                            <div class="text-danger small mb-1">{{ $message }}</div>
                        @enderror
                        <div class="row">
                            @forelse ($permissions as $permission)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                               value="{{ $permission->id }}" id="permission-{{ $permission->id }}"
                                               @checked(in_array($permission->id, old('permissions', $role->permissions->pluck('id')->all())))>
                                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                                            {{ $permission->label ?? $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">{{ __('adminlte.no_permissions') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                        </button>
                    </div>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
