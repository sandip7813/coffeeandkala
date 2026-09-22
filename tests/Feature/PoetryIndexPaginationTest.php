<?php

use App\Models\Poem;

test('the poetry index page shows only the first 16 poems and links to a second page', function () {
    Poem::factory()->active()->count(20)->create();

    $response = $this->get(route('poetry'));

    $response->assertSuccessful();
    $response->assertSee('poetry-door-tile', false);
    $response->assertViewHas('poems', fn ($poems) => $poems->total() === 20
        && $poems->count() === 16
        && $poems->lastPage() === 2);
});

test('the second page of the poetry index shows the remaining poems', function () {
    Poem::factory()->active()->count(20)->create();

    $response = $this->get(route('poetry', ['page' => 2]));

    $response->assertSuccessful();
    $response->assertViewHas('poems', fn ($poems) => $poems->currentPage() === 2
        && $poems->count() === 4);
});

test('inactive poems are excluded from the poetry index and its pagination total', function () {
    Poem::factory()->active()->count(3)->create();
    Poem::factory()->inactive()->count(5)->create();

    $response = $this->get(route('poetry'));

    $response->assertSuccessful();
    $response->assertViewHas('poems', fn ($poems) => $poems->total() === 3);
});
