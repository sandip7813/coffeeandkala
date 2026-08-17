<?php

use App\Models\Quote;
use App\Models\QuoteSchedule;

test('the command auto-assigns quotes for the next 14 days without a schedule', function () {
    Quote::factory()->count(3)->create();

    $this->artisan('quotes:assign-schedules')->assertSuccessful();

    expect(QuoteSchedule::count())->toBe(14);
    expect(QuoteSchedule::where('is_auto_assigned', true)->count())->toBe(14);
});

test('the command does not overwrite an admin-assigned schedule', function () {
    $quote = Quote::factory()->create();
    $manual = Quote::factory()->create();

    QuoteSchedule::factory()->create([
        'date' => now()->addDays(3)->toDateString(),
        'quote_id' => $manual->id,
        'is_auto_assigned' => false,
    ]);

    $this->artisan('quotes:assign-schedules')->assertSuccessful();

    $this->assertDatabaseHas('quote_schedules', [
        'date' => now()->addDays(3)->toDateString().' 00:00:00',
        'quote_id' => $manual->id,
        'is_auto_assigned' => 0,
    ]);
});
