<?php

namespace App\Actions\Quotes;

use App\Models\Quote;
use App\Models\QuoteSchedule;
use App\Models\User;
use Carbon\CarbonInterface;

class AssignQuoteToDate
{
    /**
     * Assign (or reassign) a quote to a date, marking it as manually assigned.
     * Replaces any existing schedule for that date, auto-assigned or not.
     */
    public function handle(CarbonInterface|string $date, Quote $quote, User $assignedBy): QuoteSchedule
    {
        $date = $date instanceof CarbonInterface ? $date->toDateString() : $date;

        $schedule = QuoteSchedule::query()->whereDate('date', $date)->first()
            ?? new QuoteSchedule(['date' => $date]);

        $schedule->fill([
            'quote_id' => $quote->id,
            'assigned_by' => $assignedBy->id,
            'is_auto_assigned' => false,
        ])->save();

        return $schedule;
    }
}
