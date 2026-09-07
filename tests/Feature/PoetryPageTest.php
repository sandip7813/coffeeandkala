<?php

use App\Models\MediaFile;
use App\Models\Poem;

test('the poetry collection page returns a successful response', function () {
    $poem = Poem::factory()->active()->create(['title' => 'A Real Poem']);
    MediaFile::factory()->ofType('poetry')->create([
        'mediable_type' => Poem::class,
        'mediable_id' => $poem->id,
        'role' => 'featured',
    ]);

    $response = $this->get(route('poetry'));

    $response->assertSuccessful();
    $response->assertSee('The Poetry Collection — Coffee &amp; Kala', false);
    $response->assertSee('images/poetry/hero.png', false);
    $response->assertSee('poetryBannerZoom', false);
    $response->assertSee('poetry-banner-divider-mark', false);
    $response->assertSee('A home in every poem', false);
    $response->assertSee('poems, each a door left ajar', false);
    $response->assertSee('poetry-collection', false);
    $response->assertSee('data-poetry-carousel', false);
    $response->assertSee('data-poetry-prev', false);
    $response->assertSee('data-poetry-next', false);
    $response->assertSee('poetry-door-photo', false);
    $response->assertSee('poetry-door-graphic', false);
    $response->assertSee('images/poetry/door-frame.png', false);

    $html = $response->getContent();
    expect(
        str_contains($html, 'resources/css/poetry.css') || str_contains($html, 'build/assets/poetry-')
    )->toBeTrue();
    expect(
        str_contains($html, 'resources/js/poetry.js') || str_contains($html, 'build/assets/poetry-')
    )->toBeTrue();

    $response->assertSee($poem->title, false);
    $response->assertSee(route('poetry.show', $poem->slug), false);
});

test('only active poems appear on the poetry collection page', function () {
    $active = Poem::factory()->active()->create(['title' => 'An Active Poem']);
    $pending = Poem::factory()->pending()->create(['title' => 'A Pending Poem']);
    $inactive = Poem::factory()->inactive()->create(['title' => 'An Inactive Poem']);

    $response = $this->get(route('poetry'));

    $response->assertSuccessful();
    $response->assertSee($active->title, false);
    $response->assertDontSee($pending->title, false);
    $response->assertDontSee($inactive->title, false);
});

test('the poetry collection page shows a "no poetry found" message when no poems exist', function () {
    $response = $this->get(route('poetry'));

    $response->assertSuccessful();
    $response->assertDontSee('poetry-collection', false);
    $response->assertSee('No poetry found.', false);
});

test('poetry links from the header and home navigation', function () {
    $home = $this->get(route('home'));

    $home->assertSuccessful();
    $home->assertSee(route('poetry', absolute: false), false);
});

test('a poem page opens the book at that poem\'s page', function () {
    // The controller orders newest-first, so the older poem ($first, created
    // first) ends up second in the collection — page index (3 + 1*2 = 5).
    $first = Poem::factory()->active()->create(['title' => 'First Poem', 'body' => "Line one of the first stanza.\nLine two.\n\nA second stanza line.", 'created_at' => now()->subMinute()]);
    $second = Poem::factory()->active()->create(['title' => 'Second Poem', 'body' => 'Only one line here.', 'created_at' => now()]);

    $response = $this->get(route('poetry.show', $first->slug));

    $response->assertSuccessful();
    $response->assertSee($first->title.' — The Poetry Shelf — Coffee &amp; Kala', false);
    $response->assertSee('data-poetry-book', false);
    $response->assertSee('data-start="5"', false);
    $response->assertSee('poetry-book-page--cover', false);
    $response->assertSee('poetry-book-page--card', false);
    $response->assertSee('poetry-book-page--poem', false);
    $response->assertSee('poetry-book-page--closing', false);
    $response->assertSee('poetry-book-vine', false);
    $response->assertSee('The Poetry Shelf', false);
    $response->assertSee('a bound collection, kept for slow reading', false);
    $response->assertSee('Lean into the book', false);
    $response->assertSee('to read more heartfelt poems.', false);
    $response->assertSee('poetry-stanza', false);
    $response->assertSee('Only one line here.', false);

    // Every poem is reachable from the table of contents.
    $response->assertSee($first->title, false);
    $response->assertSee($second->title, false);

    $response->assertSee(route('poetry'), false);
});

test('a poem\'s stanzas are derived from blank-line-separated paragraphs in its body', function () {
    $poem = Poem::factory()->active()->create([
        'body' => "Stanza one line one.\nStanza one line two.\n\nStanza two line one.",
    ]);

    $response = $this->get(route('poetry.show', $poem->slug));

    $response->assertSuccessful();
    $response->assertSeeInOrder([
        'Stanza one line one.',
        'Stanza one line two.',
        'Stanza two line one.',
    ], false);
});

test('poem navigation wraps around at either end of the collection', function () {
    $first = Poem::factory()->active()->create(['created_at' => now()->subMinutes(2)]);
    $last = Poem::factory()->active()->create(['created_at' => now()->subMinute()]);

    // Newest first (the controller's ordering) — $last comes before $first.
    $response = $this->get(route('poetry.show', $first->slug));

    $response->assertSuccessful();
    // $first is the last (oldest) poem, so its "next" (wrapping) is $last.
    $response->assertSee($last->title, false);
});

test('the "Previously"/"Coming up" labels are hidden when there is only one poem', function () {
    $poem = Poem::factory()->active()->create();

    $response = $this->get(route('poetry.show', $poem->slug));

    $response->assertSuccessful();
    $response->assertDontSee('Previously', false);
    $response->assertDontSee('Coming up', false);
    $response->assertDontSee('More poems nearby', false);
});

test('the "Previously"/"Coming up" labels show once there is more than one poem', function () {
    $first = Poem::factory()->active()->create(['created_at' => now()->subMinute()]);
    Poem::factory()->active()->create(['created_at' => now()]);

    $response = $this->get(route('poetry.show', $first->slug));

    $response->assertSuccessful();
    $response->assertSee('Previously', false);
    $response->assertSee('Coming up', false);
    $response->assertSee('More poems nearby', false);
});

test('an unknown poem slug is not found', function () {
    $response = $this->get('/poetry/a-poem-that-does-not-exist');

    $response->assertNotFound();
});

test('a pending or inactive poem is not reachable on the frontend', function () {
    $pending = Poem::factory()->pending()->create();
    $inactive = Poem::factory()->inactive()->create();

    $this->get(route('poetry.show', $pending->slug))->assertNotFound();
    $this->get(route('poetry.show', $inactive->slug))->assertNotFound();
});
