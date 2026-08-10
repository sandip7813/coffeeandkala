<?php

use App\Support\JournalCatalog;

test('the journal page returns a successful response', function () {
    $response = $this->get(route('journal'));
    $highlights = JournalCatalog::categoryHighlights();

    $response->assertSuccessful();
    $response->assertSee('Journal — Coffee &amp; Kala', false);
    $response->assertSee('images/journal/hero.png', false);
    $response->assertSee('journal-hero-image', false);
    $response->assertSee('journalBannerZoom', false);
    $response->assertSee('Show full banner image', false);
    $response->assertSee('The Journal', false);
    $response->assertSee('Explore by Category', false);
    $response->assertSee('journal-categories', false);
    $response->assertSee('journal-category-index', false);
    $response->assertSee('journal-category-date', false);
    $response->assertSee('Read the story', false);

    // The highlight shown per category is always the newest entry in it,
    // its category name is hyperlinked to the category page, and its date
    // renders alongside it.
    foreach ($highlights as $entry) {
        $response->assertSee($entry['title'], false);
        $response->assertSee($entry['date_label'], false);
        $response->assertSee(route('journal.category', $entry['category_id']), false);
    }

    // Only entries carrying a named category are eligible as highlights —
    // uncategorised dispatches never appear here.
    $response->assertDontSee('A Note from a Rainy Evening', false);
    $response->assertDontSee('Weathered Light', false);
    $response->assertDontSee('Midnight Margins', false);
    $response->assertDontSee('journal-departments', false);
    $response->assertDontSee('Center spread', false);
    $response->assertDontSee('Inside this edition', false);
    $response->assertSee('Collect moments,', false);
    $response->assertSee('End of edition', false);
    $response->assertDontSee('editorialSidebar', false);
    $response->assertDontSee('toggleLeftDrawer', false);
    $response->assertSee('journal-topbar', false);
    $response->assertSee('journal-reveal', false);

    $html = $response->getContent();
    expect(
        str_contains($html, 'resources/css/journal.css') || str_contains($html, 'build/assets/journal-')
    )->toBeTrue();
});

test('journal category links from the header, footer, and home navigation are real, not placeholders', function () {
    $home = $this->get(route('home'));

    $home->assertSuccessful();
    $home->assertSee(route('journal', absolute: false), false);

    foreach (JournalCatalog::categories() as $category) {
        $home->assertSee($category['name'], false);
        $home->assertSee(route('journal.category', $category['id'], absolute: false), false);
    }

    // The nav dropdown once shipped with "javascript:void(0)" placeholders —
    // guard against that regressing.
    $home->assertDontSee('javascript:void(0)">The Bigger Picture', false);
    $home->assertDontSee('javascript:void(0)">Worth Knowing', false);
    $home->assertDontSee('javascript:void(0)">Chapters Over Coffee', false);

    $journal = $this->get(route('journal'));
    $journal->assertSuccessful();
    $journal->assertSee('is-active', false);

    foreach (JournalCatalog::categories() as $category) {
        $journal->assertSee($category['name'], false);
        $journal->assertSee(route('journal.category', $category['id'], absolute: false), false);
    }
});
