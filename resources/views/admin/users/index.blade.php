@extends('adminlte::page')

@section('title', __('adminlte.users'))

@section('content_header')
    <h1 class="m-0">{{ __('adminlte.users') }}</h1>
@stop

@section('content')
    <x-adminlte-card icon="bi bi-people" title="{{ __('adminlte.users') }}" bodyClass="p-0">
        <x-slot name="tools">
            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('adminlte.new_user') }}
            </a>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-striped align-middle m-0">
                <thead>
                    <tr>
                        <th>{{ __('adminlte.name') }}</th>
                        <th>{{ __('adminlte.email') }}</th>
                        <th>Phone</th>
                        <th>{{ __('adminlte.roles') }}</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 4.5rem;">{{ __('adminlte.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td><strong>{{ $user->full_name }}</strong></td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td class="text-muted">{{ $user->phone ?: '—' }}</td>
                            <td>
                                @forelse ($user->roles as $role)
                                    <span class="badge bg-primary">{{ $role->label ?? $role->name }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <x-admin.row-actions>
                                    @if (\Illuminate\Support\Facades\Route::has('adminlte.impersonate.start') && ! $user->is(auth()->user()))
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                               href="{{ route('admin.impersonate.start', $user) }}">
                                                <i class="bi bi-incognito" aria-hidden="true"></i>
                                                <span>{{ __('adminlte.login_as') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                           href="{{ route('admin.users.edit', $user) }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                            <span>{{ __('adminlte.edit') }}</span>
                                        </a>
                                    </li>
                                    @if (auth()->user()?->isSuperAdmin() && ! $user->is(auth()->user()))
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  data-confirm-delete
                                                  data-confirm-title="Delete this user?"
                                                  data-confirm-text="{{ $user->full_name }} will be permanently removed. This cannot be undone."
                                                  data-confirm-button="Yes, delete user"
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
                        <tr><td colspan="6" class="text-center text-muted py-4">{{ __('adminlte.no_users') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot name="footer">
            {{ $users->links() }}
        </x-slot>
    </x-adminlte-card>
@stop
