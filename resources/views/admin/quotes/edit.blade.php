@extends('adminlte::page')

@section('title', __('Edit Quote'))

@section('content_header')
    <h3 class="mb-0 text-center">{{ __('Edit Quote') }}</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <x-adminlte-card icon="bi bi-chat-quote" title="{{ __('Edit Quote') }}">
                <form method="POST" action="{{ route('admin.quotes.update', $quote) }}">
                    @csrf
                    @method('PUT')

                    <x-adminlte-textarea name="text" label="{{ __('Quote text') }} *" rows="4" required>{{ old('text', $quote->text) }}</x-adminlte-textarea>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Assign to date(s)') }}</label>
                        <p class="text-muted small mb-2">{{ __('Optional — pick one or more upcoming days to schedule this quote for. Picking an already-assigned day replaces its current quote.') }}</p>
                        @error('dates')
                            <div class="text-danger small mb-1">{{ $message }}</div>
                        @enderror
                        <x-admin.quotes.date-checkboxes :schedule-dates="$scheduleDates" :selected-dates="old('dates', $selectedDates)" :current-quote-id="$quote->id" id-prefix="edit-date" />
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                        </button>
                    </div>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
