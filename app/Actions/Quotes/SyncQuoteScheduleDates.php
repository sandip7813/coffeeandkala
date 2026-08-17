<?php

namespace App\Actions\Quotes;

use App\Models\Quote;
use App\Models\QuoteSchedule;
use App\Models\User;
use Illuminate\Support\Carbon;

class SyncQuoteScheduleDates
{
    public function __construct(
        private AssignQuoteToDate $assignQuoteToDate,
        private EnsureQuoteScheduledForDate $ensureQuoteScheduledForDate,
    ) {}

    /**
     * Make the quote's schedule within the next 14 days match exactly the given dates:
     * assigns it to any newly-checked date, and frees (auto-reassigns) any date that was
     * previously assigned to this quote but is no longer in the list.
     *
     * @param  array<int, string>  $dates
     */
    public function handle(Quote $quote, array $dates, User $assignedBy): void
    {
        $window = [Carbon::today(), Carbon::today()->addDays(13)];

        $currentlyAssigned = QuoteSchedule::query()
            ->where('quote_id', $quote->id)
            ->whereDate('date', '>=', $window[0]->toDateString())
            ->whereDate('date', '<=', $window[1]->toDateString())
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->all();

        foreach ($dates as $date) {
            $this->assignQuoteToDate->handle($date, $quote, $assignedBy);
        }

        foreach (array_diff($currentlyAssigned, $dates) as $date) {
            QuoteSchedule::query()->where('quote_id', $quote->id)->whereDate('date', $date)->delete();
            $this->ensureQuoteScheduledForDate->handle(Carbon::parse($date));
        }
    }
}
