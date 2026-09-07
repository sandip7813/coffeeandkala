<?php

use App\Models\Article;
use App\Models\Category;
use App\Support\JournalCatalog;

function seedJournalHighlights(): void
{
    foreach (JournalCatalog::categories() as $category) {
        $categoryModel = Category::query()->ofType(Category::TYPE_JOURNAL)->where('slug', $category['id'])->firstOrFail();

        Article::factory()->journal()->active()->create([
            'category_id' => $categoryModel->id,
            'title' => "Newest dispatch for {$category['name']}",
        ]);
    }
}

test('the journal page returns a successful response', function () {
    seedJournalHighlights();

    $response = $this->get(route('journal'));

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

    // The highlight shown per category is the newest active article in it,
    // its category name is hyperlinked to the category page, and its date
    // renders alongside it.
    foreach (JournalCatalog::categories() as $category) {
        $article = Article::where('title', "Newest dispatch for {$category['name']}")->firstOrFail();

        $response->assertSee($article->title, false);
        $response->assertSee(route('journal.category', $category['id']), false);
    }

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

test('the journal page shows nothing published yet gracefully when no articles exist', function () {
    $response = $this->get(route('journal'));

    $response->assertSuccessful();
    $response->assertDontSee('Read the story', false);
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
