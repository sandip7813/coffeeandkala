@extends('adminlte::page')

@section('title', __('adminlte.roles'))

@section('content_header')
    <h1 class="m-0">{{ __('adminlte.roles') }}</h1>
@stop

@section('content')
    <x-adminlte-card icon="bi bi-shield-lock" title="{{ __('adminlte.roles') }}" bodyClass="p-0">
        <x-slot name="tools">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('adminlte.new_role') }}
            </a>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-striped align-middle m-0">
                <thead>
                    <tr>
                        <th>{{ __('adminlte.name') }}</th>
                        <th>{{ __('adminlte.label') }}</th>
                        <th>{{ __('adminlte.permissions') }}</th>
                        <th class="text-end" style="width: 4.5rem;">{{ __('adminlte.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td><strong>{{ $role->name }}</strong></td>
                            <td class="text-muted">{{ $role->label ?? '—' }}</td>
                            <td><span class="badge bg-secondary">{{ $role->permissions_count }}</span></td>
                            <td class="text-end">
                                <x-admin.row-actions>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                           href="{{ route('admin.roles.edit', $role) }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                            <span>{{ __('adminlte.edit') }}</span>
                                        </a>
                                    </li>
                                    @if ($role->name !== 'super_admin')
                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2"
                                                    data-bs-toggle="modal" data-bs-target="#role-permissions-{{ $role->id }}">
                                                <i class="bi bi-key" aria-hidden="true"></i>
                                                <span>{{ __('adminlte.permissions') }}</span>
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                                  data-confirm-delete
                                                  data-confirm-title="Delete this role?"
                                                  data-confirm-text="{{ $role->label ?? $role->name }} will be permanently removed. This cannot be undone."
                                                  data-confirm-button="Yes, delete role"
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
                                    @endif
                                </x-admin.row-actions>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">{{ __('adminlte.no_roles') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot name="footer">
            {{ $roles->links() }}
        </x-slot>
    </x-adminlte-card>

    @foreach ($roles as $role)
        @continue($role->name === 'super_admin')
        @include('admin.roles.partials.permission-modal', ['role' => $role, 'permissions' => $permissions])
    @endforeach
@stop
