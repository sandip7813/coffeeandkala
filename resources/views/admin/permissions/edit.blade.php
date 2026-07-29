@extends('adminlte::page')

@section('title', __('Edit Permission'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('Edit Permission') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('adminlte.home') }}</a></li>
                <li class="breadcrumb-item">{{ __('adminlte.administration') }}</li>
                <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">{{ __('adminlte.permissions') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('adminlte.edit') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
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
@stop
