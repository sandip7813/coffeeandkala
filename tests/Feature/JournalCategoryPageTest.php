<?php

use App\Support\JournalCatalog;

test('a category page lists its entries with a themed layout unique to it', function () {
    $themeMarkers = [
        'the-bigger-picture' => 'jc-picture-list',
        'worth-knowing' => 'jc-guide-grid',
        'chapters-over-coffee' => 'jc-diary-list',
    ];

    foreach (JournalCatalog::categories() as $category) {
        $response = $this->get(route('journal.category', $category['id']));
        $entries = JournalCatalog::forCategory($category['id']);

        $response->assertSuccessful();
        $response->assertSee($category['name'].' — Journal — Coffee &amp; Kala', false);
        $response->assertSee($themeMarkers[$category['id']], false);
        $response->assertSee('jc-theme--'.$category['id'], false);

        // Only this category's own theme marker is present — every other
        // category's layout is genuinely absent from the page.
        foreach ($themeMarkers as $otherId => $marker) {
            if ($otherId !== $category['id']) {
                $response->assertDontSee($marker, false);
            }
        }

        // First page shows up to the first 6 entries, newest first.
        foreach (array_slice($entries, 0, 6) as $entry) {
            $response->assertSee($entry['title']);
            $response->assertSee($entry['date_label'], false);
        }

        if (count($entries) > 6) {
            $response->assertSee(route('journal.category', $category['id']).'?page=2', false);
        }
    }
});

test('a category page paginates at six entries per page', function () {
    // Chapters Over Coffee has 12 entries, splitting evenly across two pages.
    $entries = JournalCatalog::forCategory('chapters-over-coffee');

    $page1 = $this->get(route('journal.category', 'chapters-over-coffee'));
    $page1->assertSuccessful();
    $page1->assertSee('Page 1 of 2', false);

    foreach (array_slice($entries, 0, 6) as $entry) {
        $page1->assertSee($entry['title']);
    }
    $page1->assertDontSee($entries[6]['title']);

    $page2 = $this->get(route('journal.category', 'chapters-over-coffee').'?page=2');
    $page2->assertSuccessful();
    $page2->assertSee('Page 2 of 2', false);
    $page2->assertSee($entries[6]['title']);
    $page2->assertDontSee($entries[0]['title']);
});

test('pagination controls stay hidden when everything fits on one page', function () {
    // Worth Knowing has exactly 6 entries — right at the six-per-page threshold.
    $response = $this->get(route('journal.category', 'worth-knowing'));

    $response->assertSuccessful();
    $response->assertDontSee('jc-pagination', false);
});

test('an unknown category slug 404s', function () {
    $response = $this->get('/journal/not-a-real-category');

    $response->assertNotFound();
});

test('category links from the journal index resolve to real category pages', function () {
    foreach (JournalCatalog::categories() as $category) {
        $this->get(route('journal.category', $category['id']))->assertSuccessful();
    }
});
