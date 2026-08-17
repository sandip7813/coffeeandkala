@props([
    'scheduleDates',
    'selectedDates' => [],
    'currentQuoteId' => null,
    'idPrefix' => 'date',
])

<div class="row g-2">
    @foreach ($scheduleDates as $day)
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="dates[]"
                       value="{{ $day['date'] }}" id="{{ $idPrefix }}-{{ $day['date'] }}"
                       @checked(collect($selectedDates)->contains($day['date']))>
                <label class="form-check-label" for="{{ $idPrefix }}-{{ $day['date'] }}">
                    {{ $day['label'] }}
                    @if ($day['assigned_quote_id'] && $day['assigned_quote_id'] !== $currentQuoteId)
                        <span class="badge bg-secondary ms-1" title="{{ $day['assigned_text'] }}">{{ __('Assigned to another quote') }}</span>
                    @endif
                </label>
            </div>
        </div>
    @endforeach
</div>
