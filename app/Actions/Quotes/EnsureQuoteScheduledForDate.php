<?php

namespace App\Actions\Quotes;

use App\Models\Quote;
use App\Models\QuoteSchedule;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;

class EnsureQuoteScheduledForDate
{
    /**
     * Fetch the quote schedule for the given date, auto-assigning a quote to it if one
     * hasn't been assigned yet. Admin-assigned schedules are never overwritten here.
     */
    public function handle(CarbonInterface $date): QuoteSchedule
    {
        $schedule = QuoteSchedule::with('quote', 'assignedBy')
            ->whereDate('date', $date->toDateString())
            ->first();

        if ($schedule !== null) {
            return $schedule;
        }

        try {
            $schedule = QuoteSchedule::create([
                'date' => $date->toDateString(),
                'quote_id' => $this->pickQuoteIdFor($date),
                'assigned_by' => null,
                'is_auto_assigned' => true,
            ]);
        } catch (QueryException) {
            // Another request assigned this date concurrently; use that one.
            $schedule = QuoteSchedule::query()->whereDate('date', $date->toDateString())->firstOrFail();
        }

        return $schedule->load('quote', 'assignedBy');
    }

    /**
     * Pick a random quote for the date, avoiding the quote scheduled the day before when possible.
     */
    private function pickQuoteIdFor(CarbonInterface $date): ?int
    {
        $previousQuoteId = QuoteSchedule::query()
            ->whereDate('date', $date->clone()->subDay()->toDateString())
            ->value('quote_id');

        $quoteId = Quote::query()
            ->when($previousQuoteId, fn ($query) => $query->where('id', '!=', $previousQuoteId))
            ->inRandomOrder()
            ->value('id');

        return $quoteId ?? Quote::query()->inRandomOrder()->value('id');
    }
}
