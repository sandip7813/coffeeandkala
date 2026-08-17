<?php

namespace App\Console\Commands\Quotes;

use App\Actions\Quotes\EnsureQuoteScheduledForDate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AssignUpcomingQuoteSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotes:assign-schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-assign a quote to any of the next 14 days that an admin has not already scheduled one for';

    /**
     * Execute the console command.
     */
    public function handle(EnsureQuoteScheduledForDate $ensureQuoteScheduledForDate): int
    {
        $assigned = 0;

        for ($offset = 0; $offset < 14; $offset++) {
            $schedule = $ensureQuoteScheduledForDate->handle(Carbon::today()->addDays($offset));

            if ($schedule->wasRecentlyCreated) {
                $assigned++;
            }
        }

        $this->info("Auto-assigned quotes for {$assigned} day(s) in the next 14-day window.");

        return self::SUCCESS;
    }
}
