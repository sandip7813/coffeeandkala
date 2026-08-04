@extends('adminlte::page')

@section('title', __('Edit Permission'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('Edit Permission') }}</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-adminlte-card icon="bi bi-key" title="{{ __('Edit Permission') }}">
                <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
                    @csrf
                    @method('PUT')

                    <x-adminlte-input name="name" label="{{ __('adminlte.name') }}" :value="$permission->name" required />
                    <x-adminlte-input name="label" label="{{ __('adminlte.label') }}" :value="$permission->label" />

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                        </button>
                    </div>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
