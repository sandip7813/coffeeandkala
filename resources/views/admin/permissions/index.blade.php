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
    @if ($groupedPermissions->isEmpty())
        <p class="text-center text-muted py-4">{{ __('adminlte.no_permissions') }}</p>
    @else
        <div class="accordion permissions-accordion permissions-accordion--columns" id="permissions-accordion">
            @foreach ($groupedPermissions as $group => $permissions)
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permissions as $permission)
                                            <tr>
                                                <td><code>{{ $permission->name }}</code></td>
                                                <td>{{ $permission->label }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@stop

@section('css')
    <style>
        .permissions-accordion--columns {
            column-count: 2;
            column-gap: 1rem;
        }

        /* With items broken into columns, each one sits on its own rather
           than flush against its neighbour — so, unlike a normal single-
           column accordion, every item needs its own top border and full
           corner rounding instead of only the first/last item getting it. */
        .permissions-accordion--columns .accordion-item {
            break-inside: avoid-column;
            margin-bottom: 1rem;
            border-top-width: var(--bs-accordion-border-width) !important;
            border-radius: var(--bs-accordion-border-radius) !important;
        }

        .permissions-accordion--columns .accordion-item:last-child {
            margin-bottom: 0;
        }

        .permissions-accordion--columns .accordion-item .accordion-button {
            border-radius: var(--bs-accordion-inner-border-radius) var(--bs-accordion-inner-border-radius) 0 0 !important;
        }

        .permissions-accordion--columns .accordion-item .accordion-collapse {
            border-radius: 0 0 var(--bs-accordion-border-radius) var(--bs-accordion-border-radius);
        }

        @media (max-width: 991.98px) {
            .permissions-accordion--columns {
                column-count: 1;
            }
        }
    </style>
@stop
