@extends('adminlte::page')

@section('title', __('Quotes'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('Quotes') }}</h1>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('admin.quotes.schedule.index') }}" class="btn btn-sm btn-outline-primary me-2">
                <i class="bi bi-calendar-week me-1" aria-hidden="true"></i> {{ __('Datewise Schedule') }}
            </a>
            <button type="button" class="btn btn-sm btn-outline-info me-2" data-search-toggle="#quoteSearch" aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}" aria-controls="quoteSearch">
                <i class="bi bi-search me-1" aria-hidden="true"></i> {{ __('Search') }}
            </button>
            <a href="{{ route('admin.quotes.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('New Quote') }}
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="collapse {{ $hasActiveFilters ? 'show' : '' }}" id="quoteSearch">
        <x-adminlte-card class="mb-3">
            <form method="GET" action="{{ route('admin.quotes.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="filter-text" class="form-label">{{ __('Quote text') }}</label>
                    <input type="text" id="filter-text" name="text" class="form-control" placeholder="{{ __('Search by text') }}" value="{{ $filters['text'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="filter-date-range" class="form-label">{{ __('Assigned date range') }}</label>
                    <input type="text" id="filter-date-range" class="form-control" placeholder="{{ __('Select a date range') }}" autocomplete="off" readonly
                           data-daterangepicker data-start-input="#filter-date-from" data-end-input="#filter-date-to">
                    <input type="hidden" id="filter-date-from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
                    <input type="hidden" id="filter-date-to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1" aria-hidden="true"></i> {{ __('Search') }}
                    </button>
                    <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i> {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </x-adminlte-card>
    </div>

    <x-adminlte-card icon="bi bi-chat-quote" bodyClass="p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>{{ __('Quote') }}</th>
                        <th>{{ __('Created by') }}</th>
                        <th>{{ __('Times scheduled') }}</th>
                        <th>{{ __('Upcoming date') }}</th>
                        <th class="text-end" style="width: 4.5rem;">{{ __('adminlte.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quotes as $quote)
                        <tr>
                            <td>{{ \Illuminate\Support\Str::limit($quote->text, 100) }}</td>
                            <td>{{ $quote->creator?->full_name ?? __('—') }}</td>
                            <td><span class="badge bg-info">{{ $quote->schedules_count }}</span></td>
                            <td>
                                @if ($quote->upcomingSchedule)
                                    {{ $quote->upcomingSchedule->date->format('M j, Y') }}
                                    @if ($quote->upcomingSchedule->date->isToday())
                                        <span class="badge bg-primary ms-1">{{ __('Today') }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">{{ __('—') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <x-admin.row-actions>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                           href="{{ route('admin.quotes.edit', $quote) }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                            <span>{{ __('adminlte.edit') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                           data-bs-toggle="modal" data-bs-target="#assignDatesModal{{ $quote->id }}">
                                            <i class="bi bi-calendar-plus" aria-hidden="true"></i>
                                            <span>{{ __('Assign to date(s)') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.quotes.destroy', $quote) }}"
                                              data-confirm-toggle
                                              data-confirm-title="{{ __('Delete this quote?') }}"
                                              data-confirm-text="{{ __('This cannot be undone.') }}"
                                              data-confirm-button="{{ __('Yes, delete') }}"
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
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ __('No quotes found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($quotes->hasPages())
            <div class="p-3">{{ $quotes->links() }}</div>
        @endif
    </x-adminlte-card>

    @foreach ($quotes as $quote)
        <div class="modal fade" id="assignDatesModal{{ $quote->id }}" tabindex="-1"
             aria-labelledby="assignDatesModalLabel{{ $quote->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.quotes.assign-dates', $quote) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_reopen_modal" value="assignDatesModal{{ $quote->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="assignDatesModalLabel{{ $quote->id }}">
                                {{ __('Assign to date(s)') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <blockquote class="text-muted small border-start ps-2 mb-3">{{ $quote->text }}</blockquote>
                            <p class="text-muted small mb-2">{{ __('Pick at least one upcoming day to schedule this quote for. Picking an already-assigned day replaces its current quote.') }}</p>
                            @error('dates')
                                <div class="text-danger small mb-1">{{ $message }}</div>
                            @enderror
                            <x-admin.quotes.date-checkboxes
                                :schedule-dates="$scheduleDates"
                                :selected-dates="$scheduleDates->where('assigned_quote_id', $quote->id)->pluck('date')"
                                :current-quote-id="$quote->id"
                                :id-prefix="'assign-date-'.$quote->id" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('adminlte.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@stop
