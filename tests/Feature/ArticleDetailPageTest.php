<?php

use App\Support\FeatureCatalog;
use App\Support\JournalCatalog;

test('a feature article detail page returns a successful response', function () {
    $category = FeatureCatalog::find('on-a-budget');
    $article = $category['articles'][0];

    $response = $this->get(route('features.article', ['category' => $category['id'], 'article' => $article['slug']]));

    $response->assertSuccessful();
    $response->assertSee(e($article['title']), false);
    $response->assertSee('features-theme--on-a-budget', false);
    $response->assertSee('Table of Contents', false);
    $response->assertSee('Editor&rsquo;s Note', false);
    $response->assertSee('Frequently Asked Questions', false);
    $response->assertSee('Author&rsquo;s Note', false);
    $response->assertSee('Explore the Sections', false);
    $response->assertSee('Recently Published', false);
});

test('a journal entry detail page returns a successful response', function () {
    $entry = JournalCatalog::forCategory('worth-knowing')[0];

    $response = $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => $entry['slug']]));

    $response->assertSuccessful();
    $response->assertSee(e($entry['title']), false);
    $response->assertSee('jc-theme--worth-knowing', false);
    $response->assertSee('Table of Contents', false);
    $response->assertSee('Frequently Asked Questions', false);
});

test('unknown feature article returns not found', function () {
    $this->get('/features/on-a-budget/not-a-real-article')->assertNotFound();
});

test('unknown journal entry returns not found', function () {
    $this->get('/journal/worth-knowing/not-a-real-entry')->assertNotFound();
});
