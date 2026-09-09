@extends('adminlte::page')

@section('title', __('adminlte.permissions'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('adminlte.permissions') }}</h1>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('New Permission') }}
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="accordion permissions-accordion" id="permissions-accordion">
        @forelse ($groupedPermissions as $group => $permissions)
            @php $panelId = 'permissions-group-'.\Illuminate\Support\Str::slug($group); @endphp

            <div class="accordion-item">
                <h2 class="accordion-header" id="{{ $panelId }}-heading">
                    <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#{{ $panelId }}-collapse"
                        aria-expanded="false"
                        aria-controls="{{ $panelId }}-collapse"
                    >
                        <span class="flex-grow-1">{{ $group }}</span>
                        <span class="badge text-bg-secondary me-2">{{ $permissions->count() }}</span>
                    </button>
                </h2>
                <div id="{{ $panelId }}-collapse" class="accordion-collapse collapse" aria-labelledby="{{ $panelId }}-heading">
                    <div class="accordion-body p-0">
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
                                    @foreach ($permissions as $permission)
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-muted py-4">{{ __('adminlte.no_permissions') }}</p>
        @endforelse
    </div>
@stop
