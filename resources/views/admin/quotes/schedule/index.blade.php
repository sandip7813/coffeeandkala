@extends('adminlte::page')

@section('title', __('Quote Schedule'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('Quote Schedule') }}</h1>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('admin.quotes.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-chat-quote me-1" aria-hidden="true"></i> {{ __('Manage Quotes') }}
            </a>
        </div>
    </div>
@stop

@section('content')
    <x-adminlte-card icon="bi bi-calendar-week" title="{{ __('Next 14 Days') }}" bodyClass="p-0">
        @if ($quotes->isEmpty())
            <p class="text-center text-muted py-4 mb-0">{{ __('Create a quote before assigning a daily schedule.') }}</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 12rem;">{{ __('Date') }}</th>
                            <th>{{ __('Assigned Quote') }}</th>
                            <th style="width: 12rem;">{{ __('Assigned By') }}</th>
                            <th class="text-end" style="width: 20rem;">{{ __('Change Assignment') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr>
                                <td>
                                    {{ $schedule->date->format('D, M j, Y') }}
                                    @if ($schedule->date->isToday())
                                        <span class="badge bg-primary ms-1">{{ __('Today') }}</span>
                                    @endif
                                </td>
                                <td>{{ $schedule->quote?->text ?? __('No quotes available') }}</td>
                                <td>
                                    @if ($schedule->is_auto_assigned)
                                        <span class="badge bg-secondary">{{ __('Auto-assigned') }}</span>
                                    @else
                                        {{ $schedule->assignedBy?->full_name ?? __('—') }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.quotes.schedule.update', $schedule->date->toDateString()) }}" class="d-flex gap-2 justify-content-end">
                                        @csrf
                                        @method('PUT')
                                        <select name="quote_id" class="form-select form-select-sm" style="max-width: 16rem;" required>
                                            @foreach ($quotes as $quote)
                                                <option value="{{ $quote->id }}" @selected($schedule->quote_id === $quote->id)>
                                                    {{ \Illuminate\Support\Str::limit($quote->text, 60) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            {{ __('Assign') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-adminlte-card>
@stop
