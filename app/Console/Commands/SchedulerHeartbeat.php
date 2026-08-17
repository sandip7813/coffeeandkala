<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SchedulerHeartbeat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scheduler-heartbeat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record a timestamp to confirm the scheduler is running (temporary diagnostic command)';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $now = now();

        Cache::put('scheduler-heartbeat-last-run', $now->toDateTimeString(), now()->addDays(1));

        Log::info('Scheduler heartbeat', ['ran_at' => $now->toDateTimeString()]);

        $this->info("Scheduler heartbeat recorded at {$now->toDateTimeString()}");
    }
}
