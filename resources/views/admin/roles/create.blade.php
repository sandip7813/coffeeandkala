@extends('adminlte::page')

@section('title', __('adminlte.new_role'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('adminlte.new_role') }}</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <x-adminlte-card icon="bi bi-shield-plus" title="{{ __('adminlte.new_role') }}">
                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf

                    <x-adminlte-input name="name" label="{{ __('adminlte.name') }}" required />
                    <x-adminlte-input name="label" label="{{ __('adminlte.label') }}" />

                    <div class="mb-3">
                        <label class="form-label">{{ __('adminlte.permissions') }}</label>
                        @error('permissions')
                            <div class="text-danger small mb-1">{{ $message }}</div>
                        @enderror
                        @include('admin.roles.partials.permission-checkboxes', ['checkedIds' => []])
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
