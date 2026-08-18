@extends('adminlte::page')

@section('title', __('adminlte.permissions'))

@section('content_header')
    <h1 class="m-0">{{ __('adminlte.permissions') }}</h1>
@stop

@section('content')
    <x-adminlte-card icon="bi bi-key" title="{{ __('adminlte.permissions') }}" bodyClass="p-0">
        <x-slot name="tools">
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('New Permission') }}
            </a>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>{{ __('adminlte.name') }}</th>
                        <th>{{ __('adminlte.label') }}</th>
                        <th class="text-end" style="width: 4.5rem;">{{ __('adminlte.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentGroup = null; @endphp
                    @forelse ($permissions as $permission)
                        @if ($permission->group !== $currentGroup)
                            @php $currentGroup = $permission->group; @endphp
                            <tr class="table-light">
                                <th colspan="3" class="text-uppercase small text-body-secondary">
                                    {{ $currentGroup ?? __('General') }}
                                </th>
                            </tr>
                        @endif
                        <tr>
                            <td><code>{{ $permission->name }}</code></td>
                            <td>{{ $permission->label }}</td>
                            <td class="text-end">
                                <x-admin.row-actions>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                           href="{{ route('admin.permissions.edit', $permission) }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                            <span>{{ __('adminlte.edit') }}</span>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}"
                                              data-confirm-delete
                                              data-confirm-title="Delete this permission?"
                                              data-confirm-text="{{ $permission->label ?? $permission->name }} will be permanently removed. This cannot be undone."
                                              data-confirm-button="Yes, delete permission"
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
                                </x-admin.row-actions>
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
