<?php

use App\Models\Quote;
use App\Models\QuoteSchedule;

test('the home page shows the quote assigned for today', function () {
    $quote = Quote::factory()->create(['text' => 'Today’s brew, today’s story.']);
    QuoteSchedule::factory()->create([
        'date' => now()->toDateString(),
        'quote_id' => $quote->id,
    ]);

    $this->get(route('home'))->assertOk()->assertSee($quote->text);
});

test('the home page auto-assigns a quote for today when none is scheduled yet', function () {
    Quote::factory()->create(['text' => 'Auto-assigned thought of the day.']);

    $this->get(route('home'))->assertOk()->assertSee('Auto-assigned thought of the day.');

    $this->assertDatabaseHas('quote_schedules', [
        'date' => now()->toDateString().' 00:00:00',
        'is_auto_assigned' => 1,
    ]);
});

test('the home page falls back to default copy when no quotes exist', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('stop me unless I decide');
});
