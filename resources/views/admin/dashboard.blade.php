@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="ck-dashboard-header">
        <div>
            <h3 class="mb-1">Dashboard</h3>
            <p class="text-body-secondary mb-0">Coffee &amp; Kala site overview</p>
        </div>
    </div>
@stop

@php
    $breakdowns = [
        ['title' => 'Feature chapters', 'items' => $feature_breakdown],
        ['title' => 'Journal categories', 'items' => $journal_breakdown],
    ];
    $visibleBreakdowns = collect($breakdowns)->filter(fn (array $b): bool => $b['items'] !== null)->values();

    $recentPanels = [
        ['title' => 'Recent journal', 'items' => $recent_journal, 'route' => 'admin.journals.index'],
        ['title' => 'Recent features', 'items' => $recent_features, 'route' => 'admin.features.index'],
        ['title' => 'Recent poetry', 'items' => $recent_poetry, 'route' => 'admin.poetry.index'],
    ];
    $visibleRecentPanels = collect($recentPanels)->filter(fn (array $panel): bool => $panel['items'] !== null)->values();
@endphp

@section('content')
    @if (count($stats))
        <div class="ck-dashboard-grid mb-4">
            @foreach ($stats as $stat)
                @if ($stat['route'])
                    <a href="{{ route($stat['route']) }}" class="ck-stat-card">
                        <span class="ck-stat-card__icon"><i class="bi {{ $stat['icon'] }}" aria-hidden="true"></i></span>
                        <span>
                            <span class="ck-stat-card__value d-block">{{ $stat['value'] }}</span>
                            <span class="ck-stat-card__label">{{ $stat['label'] }}</span>
                        </span>
                    </a>
                @else
                    <div class="ck-stat-card">
                        <span class="ck-stat-card__icon"><i class="bi {{ $stat['icon'] }}" aria-hidden="true"></i></span>
                        <span>
                            <span class="ck-stat-card__value d-block">{{ $stat['value'] }}</span>
                            <span class="ck-stat-card__label">{{ $stat['label'] }}</span>
                        </span>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="alert alert-secondary mb-4">
            Your role doesn't grant access to any dashboard content yet. Ask an administrator to assign the relevant permissions.
        </div>
    @endif

    @if ($visibleBreakdowns->isNotEmpty())
        <div class="row g-3 mb-3">
            @foreach ($visibleBreakdowns as $breakdown)
                <div class="col-lg-{{ max(6, intdiv(12, $visibleBreakdowns->count())) }}">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title mb-0">{{ $breakdown['title'] }}</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th class="text-end">Articles</th>
                                            <th class="text-end">Open</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($breakdown['items'] as $entry)
                                            <tr>
                                                <td>{{ $entry['name'] }}</td>
                                                <td class="text-end">{{ $entry['articles'] }}</td>
                                                <td class="text-end">
                                                    <a href="{{ $entry['href'] }}" class="btn btn-sm btn-outline-secondary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-body-secondary">Nothing here yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($visibleRecentPanels->isNotEmpty())
        <div class="row g-3">
            @foreach ($visibleRecentPanels as $panel)
                <div class="col-lg-{{ max(4, intdiv(12, $visibleRecentPanels->count())) }}">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">{{ $panel['title'] }}</h3>
                            <a href="{{ route($panel['route']) }}" class="small text-decoration-none">All entries</a>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse ($panel['items'] as $item)
                                    <li class="list-group-item">
                                        <a href="{{ $item['href'] }}" class="d-flex justify-content-between align-items-center gap-2 text-decoration-none text-body">
                                            <div class="ck-recent-item-text">
                                                <div class="fw-semibold text-truncate">{{ $item['title'] }}</div>
                                                @isset($item['tag'])
                                                    <small class="text-body-secondary text-truncate d-block">{{ $item['tag'] }}</small>
                                                @endisset
                                            </div>
                                            <small class="text-body-secondary text-nowrap">{{ $item['date_label'] }}</small>
                                        </a>
                                    </li>
                                @empty
                                    <li class="list-group-item text-body-secondary">Nothing here yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@stop
