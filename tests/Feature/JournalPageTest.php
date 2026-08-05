<?php

test('the journal page returns a successful response', function () {
    $response = $this->get(route('journal'));

    $response->assertSuccessful();
    $response->assertSee('Journal — Coffee &amp; Kala', false);
    $response->assertSee('images/journal/banner.jpg', false);
    $response->assertSee('journalBannerZoom', false);
    $response->assertSee('Show full banner image', false);
    $response->assertSee('journal-banner-divider-stroke', false);
    $response->assertSee('The Journal', false);
    $response->assertSee('Thoughts. Experiences. Stories that stay.', false);
    $response->assertSee('Travel Diaries', false);
    $response->assertSee('Destination Guides', false);
    $response->assertSee('Local Stories', false);
    $response->assertSee('Life on the Road', false);
    $response->assertSee('Letters from a Slow Train', false);
    $response->assertSee('A Note from a Rainy Evening', false);
    $response->assertSee('Valley After Rain', false);
    $response->assertSee('Weathered Light', false);
    $response->assertSee('journal-sheet', false);
    $response->assertSee('journal-lead', false);
    $response->assertSee('journal-columns', false);
    $response->assertSee('journal-column-media', false);
    $response->assertSee('journal-brief-media', false);
    $response->assertSee('photo-1501339847302', false);
    $response->assertSee('photo-1455390582262', false);
    $response->assertSee('photo-1506744038136', false);
    $response->assertSee('photo-1488646953014', false);
    $response->assertDontSee('journal-departments', false);
    $response->assertDontSee('Center spread', false);
    $response->assertDontSee('Inside this edition', false);
    $response->assertSee('journal-pagination', false);
    $response->assertSee('Page 1 of 3', false);
    $response->assertSee('javascript:void(0)', false);
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

test('journal links from the header, footer, and home navigation', function () {
    $home = $this->get(route('home'));

    $home->assertSuccessful();
    $home->assertSee(route('journal', absolute: false), false);
    $home->assertSee('The Bigger Picture', false);
    $home->assertSee('Worth Knowing', false);
    $home->assertSee('Chapters Over Coffee', false);

    $journal = $this->get(route('journal'));
    $journal->assertSuccessful();
    $journal->assertSee('is-active', false);
    $journal->assertSee('The Bigger Picture', false);
    $journal->assertSee('Worth Knowing', false);
    $journal->assertSee('Chapters Over Coffee', false);
});
