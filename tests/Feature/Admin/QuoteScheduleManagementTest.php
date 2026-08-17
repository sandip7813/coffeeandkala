<?php

use App\Models\Quote;
use App\Models\QuoteSchedule;
use App\Models\User;

beforeEach(fn () => seedRbac());

test('super admin can view the schedule and it auto-assigns the next 14 days', function () {
    $user = User::factory()->superAdmin()->create();
    Quote::factory()->count(3)->create();

    $this->actingAs($user)
        ->get(route('admin.quotes.schedule.index'))
        ->assertOk();

    expect(QuoteSchedule::count())->toBe(14);
    expect(QuoteSchedule::where('is_auto_assigned', true)->count())->toBe(14);
});

test('an auto-assigned schedule keeps the same quote on repeat visits', function () {
    $user = User::factory()->superAdmin()->create();
    Quote::factory()->count(3)->create();

    $this->actingAs($user)->get(route('admin.quotes.schedule.index'));
    $firstQuoteId = QuoteSchedule::whereDate('date', now()->toDateString())->value('quote_id');

    $this->actingAs($user)->get(route('admin.quotes.schedule.index'));
    $secondQuoteId = QuoteSchedule::whereDate('date', now()->toDateString())->value('quote_id');

    expect($secondQuoteId)->toBe($firstQuoteId);
});

test('super admin can assign a quote to a specific date within the next 14 days', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create();
    $date = now()->addDays(5)->toDateString();

    $response = $this->actingAs($user)->put(route('admin.quotes.schedule.update', $date), [
        'quote_id' => $quote->id,
    ]);

    $response->assertRedirect(route('admin.quotes.schedule.index'));

    $this->assertDatabaseHas('quote_schedules', [
        'date' => $date.' 00:00:00',
        'quote_id' => $quote->id,
        'assigned_by' => $user->id,
        'is_auto_assigned' => 0,
    ]);
});

test('an admin assignment overrides an existing auto-assigned schedule', function () {
    $user = User::factory()->superAdmin()->create();
    $auto = Quote::factory()->create();
    $manual = Quote::factory()->create();
    $date = now()->toDateString();

    QuoteSchedule::factory()->create([
        'date' => $date,
        'quote_id' => $auto->id,
        'is_auto_assigned' => true,
    ]);

    $this->actingAs($user)->put(route('admin.quotes.schedule.update', $date), [
        'quote_id' => $manual->id,
    ]);

    $this->assertDatabaseHas('quote_schedules', [
        'date' => $date.' 00:00:00',
        'quote_id' => $manual->id,
        'is_auto_assigned' => 0,
    ]);
});

test('a quote cannot be assigned to a date beyond the next 14 days', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create();
    $date = now()->addDays(20)->toDateString();

    $response = $this->actingAs($user)->put(route('admin.quotes.schedule.update', $date), [
        'quote_id' => $quote->id,
    ]);

    $response->assertSessionHasErrors('date');
    $this->assertDatabaseMissing('quote_schedules', ['date' => $date]);
});

test('a quote cannot be assigned to a past date', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create();
    $date = now()->subDay()->toDateString();

    $response = $this->actingAs($user)->put(route('admin.quotes.schedule.update', $date), [
        'quote_id' => $quote->id,
    ]);

    $response->assertSessionHasErrors('date');
});

test('editors cannot access the quote schedule', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)->get(route('admin.quotes.schedule.index'))->assertForbidden();
});
