<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\QuoteSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteSchedule>
 */
class QuoteScheduleFactory extends Factory
{
    protected $model = QuoteSchedule::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => now()->toDateString(),
            'quote_id' => Quote::factory(),
            'assigned_by' => null,
            'is_auto_assigned' => true,
        ];
    }
}
