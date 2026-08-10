<?php

use App\Support\PoetryCatalog;

test('the poetry collection page returns a successful response', function () {
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

    foreach (PoetryCatalog::all() as $poem) {
        $response->assertSee($poem['title'], false);
        $response->assertSee(route('poetry.show', $poem['slug']), false);
    }
});

test('poetry links from the header and home navigation', function () {
    $home = $this->get(route('home'));

    $home->assertSuccessful();
    $home->assertSee(route('poetry', absolute: false), false);
});

test('a poem page reads as a single, distraction-free room', function () {
    $poem = PoetryCatalog::find('the-weight-of-rain');
    $neighbours = PoetryCatalog::neighbours('the-weight-of-rain');

    $response = $this->get(route('poetry.show', 'the-weight-of-rain'));

    $response->assertSuccessful();
    $response->assertSee($poem['title'].' — The Poetry Collection — Coffee &amp; Kala', false);
    $response->assertSee($poem['mood'], false);
    $response->assertSee('poetry-room', false);
    $response->assertSee('poetry-stanza', false);

    foreach ($poem['stanzas'] as $stanza) {
        foreach ($stanza as $line) {
            $response->assertSee($line, false);
        }
    }

    $response->assertSee(route('poetry.show', $neighbours['prev']['slug']), false);
    $response->assertSee(route('poetry.show', $neighbours['next']['slug']), false);
    $response->assertSee($neighbours['prev']['title'], false);
    $response->assertSee($neighbours['next']['title'], false);
    $response->assertSee(route('poetry'), false);
});

test('poem navigation wraps around at either end of the collection', function () {
    $poems = PoetryCatalog::all();
    $first = $poems[0];
    $last = $poems[count($poems) - 1];

    $neighboursOfFirst = PoetryCatalog::neighbours($first['slug']);
    expect($neighboursOfFirst['prev']['slug'])->toBe($last['slug']);

    $neighboursOfLast = PoetryCatalog::neighbours($last['slug']);
    expect($neighboursOfLast['next']['slug'])->toBe($first['slug']);
});

test('an unknown poem slug is not found', function () {
    $response = $this->get('/poetry/a-poem-that-does-not-exist');

    $response->assertNotFound();
});
