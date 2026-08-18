<?php

use App\Models\Quote;
use App\Models\QuoteSchedule;
use App\Models\User;

beforeEach(fn () => seedRbac());

test('super admin can view the quotes list', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create(['text' => 'Every cup tells a story.']);

    $this->actingAs($user)
        ->get(route('admin.quotes.index'))
        ->assertOk()
        ->assertSee($quote->text);
});

test('super admin can create a quote', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)->post(route('admin.quotes.store'), [
        'text' => 'Coffee first, kala always.',
    ]);

    $response->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseHas('quotes', [
        'text' => 'Coffee first, kala always.',
        'created_by' => $user->id,
    ]);
});

test('super admin can assign a new quote to multiple dates on creation', function () {
    $user = User::factory()->superAdmin()->create();

    $firstDate = now()->addDays(2)->toDateString();
    $secondDate = now()->addDays(5)->toDateString();

    $response = $this->actingAs($user)->post(route('admin.quotes.store'), [
        'text' => 'Assigned straight away.',
        'dates' => [$firstDate, $secondDate],
    ]);

    $response->assertRedirect(route('admin.quotes.index'));

    $quote = Quote::where('text', 'Assigned straight away.')->firstOrFail();

    $this->assertDatabaseHas('quote_schedules', [
        'date' => $firstDate.' 00:00:00',
        'quote_id' => $quote->id,
        'assigned_by' => $user->id,
        'is_auto_assigned' => 0,
    ]);
    $this->assertDatabaseHas('quote_schedules', [
        'date' => $secondDate.' 00:00:00',
        'quote_id' => $quote->id,
        'assigned_by' => $user->id,
        'is_auto_assigned' => 0,
    ]);
});

test('a quote created without a date is auto-assigned to the next free date', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)->post(route('admin.quotes.store'), [
        'text' => 'No date picked.',
    ]);

    $response->assertRedirect(route('admin.quotes.index'));

    $quote = Quote::where('text', 'No date picked.')->firstOrFail();

    $this->assertDatabaseHas('quote_schedules', [
        'date' => now()->toDateString().' 00:00:00',
        'quote_id' => $quote->id,
        'assigned_by' => null,
        'is_auto_assigned' => 1,
    ]);
});

test('a quote created without a date skips past a fully-occupied 14-day window', function () {
    $user = User::factory()->superAdmin()->create();

    for ($offset = 0; $offset < 14; $offset++) {
        QuoteSchedule::factory()->create(['date' => now()->addDays($offset)->toDateString()]);
    }

    $response = $this->actingAs($user)->post(route('admin.quotes.store'), [
        'text' => 'Fifteenth day quote.',
    ]);

    $response->assertRedirect(route('admin.quotes.index'));

    $quote = Quote::where('text', 'Fifteenth day quote.')->firstOrFail();

    $this->assertDatabaseHas('quote_schedules', [
        'date' => now()->addDays(14)->toDateString().' 00:00:00',
        'quote_id' => $quote->id,
    ]);
});

test('assigning a new quote to a date replaces the existing assignment for that date', function () {
    $user = User::factory()->superAdmin()->create();
    $existing = Quote::factory()->create();
    $date = now()->addDays(1)->toDateString();

    QuoteSchedule::factory()->create(['date' => $date, 'quote_id' => $existing->id]);

    $this->actingAs($user)->post(route('admin.quotes.store'), [
        'text' => 'Newer quote wins.',
        'dates' => [$date],
    ]);

    $quote = Quote::where('text', 'Newer quote wins.')->firstOrFail();

    $this->assertDatabaseHas('quote_schedules', [
        'date' => $date.' 00:00:00',
        'quote_id' => $quote->id,
    ]);
    expect(QuoteSchedule::whereDate('date', $date)->count())->toBe(1);
});

test('a new quote cannot be assigned to a date beyond the next 14 days', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)->post(route('admin.quotes.store'), [
        'text' => 'Too far out.',
        'dates' => [now()->addDays(20)->toDateString()],
    ]);

    $response->assertSessionHasErrors('dates.0');
    $this->assertDatabaseMissing('quotes', ['text' => 'Too far out.']);
});

test('super admin can edit a quote', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create(['text' => 'Old text']);

    $response = $this->actingAs($user)->put(route('admin.quotes.update', $quote), [
        'text' => 'New text',
    ]);

    $response->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseHas('quotes', [
        'id' => $quote->id,
        'text' => 'New text',
    ]);
});

test('super admin can optionally assign dates while editing a quote', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create(['text' => 'Old text']);
    $date = now()->addDays(3)->toDateString();

    $response = $this->actingAs($user)->put(route('admin.quotes.update', $quote), [
        'text' => 'New text',
        'dates' => [$date],
    ]);

    $response->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseHas('quote_schedules', [
        'date' => $date.' 00:00:00',
        'quote_id' => $quote->id,
        'assigned_by' => $user->id,
        'is_auto_assigned' => 0,
    ]);
});

test('unchecking a previously assigned date and saving frees it up again', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create(['text' => 'Old text']);
    $otherQuote = Quote::factory()->create();
    $date = now()->toDateString();

    QuoteSchedule::factory()->create([
        'date' => $date,
        'quote_id' => $quote->id,
        'assigned_by' => $user->id,
        'is_auto_assigned' => false,
    ]);

    // Editing with the date's checkbox left unchecked (i.e. absent from the payload,
    // exactly like an unchecked HTML checkbox) should release that date rather than
    // silently keeping the old assignment.
    $this->actingAs($user)->put(route('admin.quotes.update', $quote), [
        'text' => 'New text',
    ]);

    $this->assertDatabaseMissing('quote_schedules', [
        'date' => $date.' 00:00:00',
        'quote_id' => $quote->id,
        'assigned_by' => $user->id,
        'is_auto_assigned' => 0,
    ]);
    // The date still has exactly one schedule row (auto-reassigned), not deleted outright.
    expect(QuoteSchedule::whereDate('date', $date)->count())->toBe(1);
});

test('unchecking a previously assigned date in the assign-dates modal frees it up again', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create();
    Quote::factory()->create();
    $keptDate = now()->addDays(1)->toDateString();
    $droppedDate = now()->addDays(2)->toDateString();

    QuoteSchedule::factory()->create(['date' => $keptDate, 'quote_id' => $quote->id, 'assigned_by' => $user->id, 'is_auto_assigned' => false]);
    QuoteSchedule::factory()->create(['date' => $droppedDate, 'quote_id' => $quote->id, 'assigned_by' => $user->id, 'is_auto_assigned' => false]);

    // Only $keptDate is resubmitted — $droppedDate was unchecked.
    $this->actingAs($user)->put(route('admin.quotes.assign-dates', $quote), [
        'dates' => [$keptDate],
    ]);

    $this->assertDatabaseHas('quote_schedules', [
        'date' => $keptDate.' 00:00:00',
        'quote_id' => $quote->id,
    ]);
    $this->assertDatabaseMissing('quote_schedules', [
        'date' => $droppedDate.' 00:00:00',
        'quote_id' => $quote->id,
        'assigned_by' => $user->id,
        'is_auto_assigned' => 0,
    ]);
});

test('super admin can assign an existing quote to date(s) from the quotes list', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create();
    $date = now()->addDays(4)->toDateString();

    $response = $this->actingAs($user)->put(route('admin.quotes.assign-dates', $quote), [
        'dates' => [$date],
    ]);

    $response->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseHas('quote_schedules', [
        'date' => $date.' 00:00:00',
        'quote_id' => $quote->id,
        'assigned_by' => $user->id,
        'is_auto_assigned' => 0,
    ]);
});

test('assigning a quote to dates from the quotes list requires at least one date', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.quotes.assign-dates', $quote), [
        'dates' => [],
    ]);

    $response->assertSessionHasErrors('dates');
});

test('assigning a quote to a date beyond the next 14 days from the quotes list fails', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.quotes.assign-dates', $quote), [
        'dates' => [now()->addDays(20)->toDateString()],
    ]);

    $response->assertSessionHasErrors('dates.0');
});

test('editors cannot assign quotes to dates from the quotes list', function () {
    $user = User::factory()->editor()->create();
    $quote = Quote::factory()->create();

    $this->actingAs($user)->put(route('admin.quotes.assign-dates', $quote), [
        'dates' => [now()->toDateString()],
    ])->assertForbidden();
});

test('super admin can delete a quote', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create();

    $response = $this->actingAs($user)->delete(route('admin.quotes.destroy', $quote));

    $response->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseMissing('quotes', ['id' => $quote->id]);
});

test('quotes list shows the next upcoming scheduled date for each quote', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create();

    QuoteSchedule::factory()->create(['quote_id' => $quote->id, 'date' => now()->subDays(2)->toDateString()]);
    QuoteSchedule::factory()->create(['quote_id' => $quote->id, 'date' => now()->addDays(5)->toDateString()]);
    QuoteSchedule::factory()->create(['quote_id' => $quote->id, 'date' => now()->addDays(1)->toDateString()]);

    // The soonest of the three (tomorrow) should win, not the past date or the later one.
    expect($quote->upcomingSchedule->date->toDateString())->toBe(now()->addDays(1)->toDateString());

    $this->actingAs($user)
        ->get(route('admin.quotes.index'))
        ->assertOk()
        ->assertSee(now()->addDays(1)->format('M j, Y'));
});

test('quotes list shows a dash for a quote with no upcoming schedule', function () {
    $user = User::factory()->superAdmin()->create();
    $quote = Quote::factory()->create(['text' => 'Never scheduled quote']);

    QuoteSchedule::factory()->create(['quote_id' => $quote->id, 'date' => now()->subDays(3)->toDateString()]);

    $response = $this->actingAs($user)->get(route('admin.quotes.index'));

    $response->assertOk()->assertSee($quote->text);
});

test('quotes list can be filtered by text', function () {
    $user = User::factory()->superAdmin()->create();

    $match = Quote::factory()->create(['text' => 'Brewed with love.']);
    Quote::factory()->create(['text' => 'Painted with joy.']);

    $response = $this->actingAs($user)->get(route('admin.quotes.index', ['text' => 'Brewed']));

    $response->assertOk()
        ->assertSee($match->text)
        ->assertDontSee('Painted with joy.');
});

test('quotes list can be filtered by assigned date range', function () {
    $user = User::factory()->superAdmin()->create();

    $inRange = Quote::factory()->create(['text' => 'In range quote']);
    $outOfRange = Quote::factory()->create(['text' => 'Out of range quote']);

    QuoteSchedule::factory()->create(['quote_id' => $inRange->id, 'date' => now()->addDays(2)->toDateString()]);
    QuoteSchedule::factory()->create(['quote_id' => $outOfRange->id, 'date' => now()->addDays(10)->toDateString()]);

    $response = $this->actingAs($user)->get(route('admin.quotes.index', [
        'date_from' => now()->toDateString(),
        'date_to' => now()->addDays(3)->toDateString(),
    ]));

    // Not assertDontSee($outOfRange->text): even filtered off the list, it can still
    // legitimately appear in another row's "assign to date(s)" modal, as the quote
    // currently occupying one of the 14 selectable days — so check the row itself is gone.
    $response->assertOk()
        ->assertSee($inRange->text)
        ->assertDontSee(route('admin.quotes.edit', $outOfRange), false);
});

test('editors cannot manage quotes', function () {
    $user = User::factory()->editor()->create();
    $quote = Quote::factory()->create();

    $this->actingAs($user)->get(route('admin.quotes.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.quotes.create'))->assertForbidden();
    $this->actingAs($user)->post(route('admin.quotes.store'), ['text' => 'x'])->assertForbidden();
    $this->actingAs($user)->put(route('admin.quotes.update', $quote), ['text' => 'x'])->assertForbidden();
    $this->actingAs($user)->delete(route('admin.quotes.destroy', $quote))->assertForbidden();
});

test('admins without the quotes permission cannot manage quotes', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->get(route('admin.quotes.index'))->assertForbidden();
});

test('guests are redirected to login', function () {
    $this->get(route('admin.quotes.index'))->assertRedirect(route('login'));
});

test('a user with only view-quotes can list quotes but cannot create, edit, or delete', function () {
    $user = userWithPermission('view-quotes');
    $quote = Quote::factory()->create();

    $this->actingAs($user)->get(route('admin.quotes.index'))->assertOk();
    $this->actingAs($user)->get(route('admin.quotes.create'))->assertForbidden();
    $this->actingAs($user)->post(route('admin.quotes.store'), ['text' => 'x'])->assertForbidden();
    $this->actingAs($user)->get(route('admin.quotes.edit', $quote))->assertForbidden();
    $this->actingAs($user)->put(route('admin.quotes.update', $quote), ['text' => 'x'])->assertForbidden();
    $this->actingAs($user)->delete(route('admin.quotes.destroy', $quote))->assertForbidden();
});

test('a user with only create-quotes can add a quote', function () {
    $user = userWithPermission('create-quotes');

    $this->actingAs($user)
        ->get(route('admin.quotes.create'))
        ->assertOk();

    $this->actingAs($user)
        ->post(route('admin.quotes.store'), ['text' => 'Granted by create-quotes.'])
        ->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseHas('quotes', ['text' => 'Granted by create-quotes.']);
});

test('a user with only edit-quotes can update a quote', function () {
    $user = userWithPermission('edit-quotes');
    $quote = Quote::factory()->create(['text' => 'Old text']);

    $this->actingAs($user)
        ->put(route('admin.quotes.update', $quote), ['text' => 'New text'])
        ->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseHas('quotes', ['id' => $quote->id, 'text' => 'New text']);
});

test('a user with only delete-quotes can delete a quote', function () {
    $user = userWithPermission('delete-quotes');
    $quote = Quote::factory()->create();

    $this->actingAs($user)
        ->delete(route('admin.quotes.destroy', $quote))
        ->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseMissing('quotes', ['id' => $quote->id]);
});

test('a user can edit and delete their own quote without any quote permission', function () {
    $user = userWithPermission('view-quotes');
    $quote = Quote::factory()->create(['text' => 'Old text', 'created_by' => $user->id]);

    $this->actingAs($user)
        ->get(route('admin.quotes.edit', $quote))
        ->assertOk();

    $this->actingAs($user)
        ->put(route('admin.quotes.update', $quote), ['text' => 'New text'])
        ->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseHas('quotes', ['id' => $quote->id, 'text' => 'New text']);

    $this->actingAs($user)
        ->delete(route('admin.quotes.destroy', $quote))
        ->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseMissing('quotes', ['id' => $quote->id]);
});

test('a user without edit-quotes or delete-quotes cannot manage someone else\'s quote', function () {
    $owner = User::factory()->editor()->create();
    $user = userWithPermission('view-quotes');
    $quote = Quote::factory()->create(['text' => 'Old text', 'created_by' => $owner->id]);

    $this->actingAs($user)->get(route('admin.quotes.edit', $quote))->assertForbidden();
    $this->actingAs($user)->put(route('admin.quotes.update', $quote), ['text' => 'New text'])->assertForbidden();
    $this->actingAs($user)->delete(route('admin.quotes.destroy', $quote))->assertForbidden();

    $this->assertDatabaseHas('quotes', ['id' => $quote->id, 'text' => 'Old text']);
});

test('a user with edit-quotes and delete-quotes can manage someone else\'s quote', function () {
    $owner = User::factory()->editor()->create();
    $user = userWithPermission(['view-quotes', 'edit-quotes', 'delete-quotes']);
    $quote = Quote::factory()->create(['text' => 'Old text', 'created_by' => $owner->id]);

    $this->actingAs($user)
        ->put(route('admin.quotes.update', $quote), ['text' => 'New text'])
        ->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseHas('quotes', ['id' => $quote->id, 'text' => 'New text']);

    $this->actingAs($user)
        ->delete(route('admin.quotes.destroy', $quote))
        ->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseMissing('quotes', ['id' => $quote->id]);
});

test('a user with only assign-quote-dates can assign a quote to a date', function () {
    $user = userWithPermission('assign-quote-dates');
    $quote = Quote::factory()->create();
    $date = now()->addDays(2)->toDateString();

    $this->actingAs($user)
        ->put(route('admin.quotes.assign-dates', $quote), ['dates' => [$date]])
        ->assertRedirect(route('admin.quotes.index'));

    $this->assertDatabaseHas('quote_schedules', ['date' => $date.' 00:00:00', 'quote_id' => $quote->id]);
});
