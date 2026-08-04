@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-2">
        <div>
            <h3 class="mb-1">Dashboard</h3>
            <p class="text-body-secondary mb-0">Coffee &amp; Kala site overview</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
            <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i> View site
        </a>
    </div>
@stop

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('gallery') }}" class="text-decoration-none text-body d-block">
                <x-adminlte-info-box title="{{ $gallery_plates }}" text="Gallery plates" icon="bi bi-images" theme="primary" />
            </a>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('studio') }}" class="text-decoration-none text-body d-block">
                <x-adminlte-info-box title="{{ $studio_works }}" text="Studio works" icon="bi bi-palette" theme="success" />
            </a>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('journal') }}" class="text-decoration-none text-body d-block">
                <x-adminlte-info-box title="{{ $journal_entries }}" text="Journal entries" icon="bi bi-journal-richtext" theme="warning" />
            </a>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('features') }}" class="text-decoration-none text-body d-block">
                <x-adminlte-info-box title="{{ $feature_articles }}" text="Feature articles" icon="bi bi-bookmark-star" theme="danger" />
            </a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <x-adminlte-info-box title="{{ $feature_categories }}" text="Feature chapters" icon="bi bi-collection" theme="info" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            @if (auth()->user()?->can('manage-users'))
                <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-body d-block">
                    <x-adminlte-info-box title="{{ $users }}" text="Team members" icon="bi bi-people" theme="secondary" />
                </a>
            @else
                <x-adminlte-info-box title="{{ $users }}" text="Team members" icon="bi bi-people" theme="secondary" />
            @endif
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            @if (auth()->user()?->can('manage-roles'))
                <a href="{{ route('admin.roles.index') }}" class="text-decoration-none text-body d-block">
                    <x-adminlte-info-box title="{{ $roles }}" text="Roles" icon="bi bi-shield-lock" theme="dark" />
                </a>
            @else
                <x-adminlte-info-box title="{{ $roles }}" text="Roles" icon="bi bi-shield-lock" theme="dark" />
            @endif
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            @if (auth()->user()?->can('manage-permissions'))
                <a href="{{ route('admin.permissions.index') }}" class="text-decoration-none text-body d-block">
                    <x-adminlte-info-box title="{{ $permissions }}" text="Permissions" icon="bi bi-key" theme="primary" />
                </a>
            @else
                <x-adminlte-info-box title="{{ $permissions }}" text="Permissions" icon="bi bi-key" theme="primary" />
            @endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title mb-0">Feature chapters</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Chapter</th>
                                    <th class="text-end">Articles</th>
                                    <th class="text-end">Open</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($feature_breakdown as $chapter)
                                    <tr>
                                        <td>{{ $chapter['name'] }}</td>
                                        <td class="text-end">{{ $chapter['articles'] }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('features.show', $chapter['id']) }}" class="btn btn-sm btn-outline-secondary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Recent journal</h3>
                    <a href="{{ route('journal') }}" class="small text-decoration-none">All entries</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach ($recent_journal as $entry)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $entry['title'] }}</div>
                                        <small class="text-body-secondary">{{ $entry['tag'] }}</small>
                                    </div>
                                    <small class="text-body-secondary text-nowrap">{{ $entry['date_label'] }}</small>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
