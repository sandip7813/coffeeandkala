@extends('adminlte::page')

@section('title', __('New Permission'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('New Permission') }}</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-adminlte-card icon="bi bi-key" title="{{ __('New Permission') }}">
                <form method="POST" action="{{ route('admin.permissions.store') }}">
                    @csrf

                    <x-adminlte-input name="name" label="{{ __('adminlte.name') }}" placeholder="e.g. manage-articles" required />
                    <x-adminlte-input name="label" label="{{ __('adminlte.label') }}" placeholder="e.g. Manage Articles" />
                    <x-adminlte-input name="group" label="{{ __('Group') }}" placeholder="e.g. Articles" />

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
