<?php

namespace App\Actions\Quotes;

use App\Models\QuoteSchedule;
use Illuminate\Support\Carbon;

class FindNextFreeScheduleDate
{
    /**
     * Find the earliest date, starting today, that has no quote scheduled yet.
     * Looks past the normal 14-day assignment window if every one of those days is taken.
     */
    public function handle(): Carbon
    {
        $scheduledDates = QuoteSchedule::query()
            ->where('date', '>=', Carbon::today())
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $date = Carbon::today();

        while ($scheduledDates->has($date->toDateString())) {
            $date = $date->clone()->addDay();
        }

        return $date;
    }
}
