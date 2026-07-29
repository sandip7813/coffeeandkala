@extends('adminlte::page')

@section('title', __('adminlte.permissions'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('adminlte.permissions') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('adminlte.home') }}</a></li>
                <li class="breadcrumb-item">{{ __('adminlte.administration') }}</li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('adminlte.permissions') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @if (session('status'))
        <x-adminlte-alert theme="success" dismissible>{{ session('status') }}</x-adminlte-alert>
    @endif

    <x-adminlte-card icon="bi bi-key" title="{{ __('adminlte.permissions') }}" bodyClass="p-0">
        <div class="p-3 border-bottom">
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('New Permission') }}
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('adminlte.name') }}</th>
                        <th>{{ __('adminlte.label') }}</th>
                        <th class="text-end">{{ __('adminlte.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permissions as $permission)
                        <tr>
                            <td><code>{{ $permission->name }}</code></td>
                            <td>{{ $permission->label }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.permissions.edit', $permission) }}"
                                   class="btn btn-sm btn-outline-secondary" aria-label="{{ __('adminlte.edit') }}">
                                    <i class="bi bi-pencil" aria-hidden="true"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}"
                                      onsubmit="return confirm('{{ __('adminlte.confirm_delete') }}');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" aria-label="{{ __('adminlte.delete') }}">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">{{ __('adminlte.no_permissions') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($permissions->hasPages())
            <div class="p-3">{{ $permissions->links() }}</div>
        @endif
    </x-adminlte-card>
@stop
