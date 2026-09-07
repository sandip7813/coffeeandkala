<?php

use App\Models\Article;
use App\Models\Category;
use App\Support\JournalCatalog;
use Illuminate\Support\Str;

/**
 * @return list<Article>
 */
function seedJournalCategoryArticles(string $slug, int $count): array
{
    $category = Category::query()->ofType(Category::TYPE_JOURNAL)->where('slug', $slug)->firstOrFail();

    return collect(range(1, $count))
        ->map(fn (int $i): Article => Article::factory()->journal()->active()->create([
            'category_id' => $category->id,
            'title' => "{$slug} entry {$i}",
            'introduction' => str_repeat('A long introduction paragraph for excerpt truncation testing. ', 10),
            // Spread creation timestamps out so "newest first" ordering is
            // deterministic instead of relying on same-second creation order.
            'created_at' => now()->subMinutes($count - $i),
        ]))
        ->all();
}

test('a category page lists its entries with a themed layout unique to it', function () {
    $themeMarkers = [
        'the-bigger-picture' => 'jc-picture-list',
        'worth-knowing' => 'jc-guide-grid',
        'chapters-over-coffee' => 'jc-diary-list',
    ];

    foreach (JournalCatalog::categories() as $category) {
        $articles = seedJournalCategoryArticles($category['id'], 3);

        $response = $this->get(route('journal.category', $category['id']));

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

        foreach ($articles as $article) {
            $response->assertSee($article->title);
        }
    }
});

test('a category page paginates at six entries per page', function () {
    $articles = seedJournalCategoryArticles('chapters-over-coffee', 12);
    // Newest first — same ordering the controller queries with.
    $newestFirst = array_reverse($articles);

    $page1 = $this->get(route('journal.category', 'chapters-over-coffee'));
    $page1->assertSuccessful();
    $page1->assertSee('Page 1 of 2', false);

    foreach (array_slice($newestFirst, 0, 6) as $article) {
        $page1->assertSee($article->title);
    }
    $page1->assertDontSee($newestFirst[6]->title);

    $page2 = $this->get(route('journal.category', 'chapters-over-coffee').'?page=2');
    $page2->assertSuccessful();
    $page2->assertSee('Page 2 of 2', false);
    $page2->assertSee($newestFirst[6]->title);
    $page2->assertDontSee($newestFirst[0]->title);
});

test('pagination controls stay hidden when everything fits on one page', function () {
    seedJournalCategoryArticles('worth-knowing', 6);

    $response = $this->get(route('journal.category', 'worth-knowing'));

    $response->assertSuccessful();
    $response->assertDontSee('jc-pagination', false);
});

test('a journal category with no entries shows a no-data message instead of the entry list', function () {
    $themeMarkers = [
        'the-bigger-picture' => 'jc-picture-list',
        'worth-knowing' => 'jc-guide-grid',
        'chapters-over-coffee' => 'jc-diary-list',
    ];

    foreach (JournalCatalog::categories() as $category) {
        $response = $this->get(route('journal.category', $category['id']));

        $response->assertSuccessful();
        $response->assertSee('jc-empty', false);
        $response->assertDontSee($themeMarkers[$category['id']], false);
        $response->assertDontSee('jc-pagination', false);
    }
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

test('every journal category page truncates entry excerpts to 80 characters', function () {
    foreach (JournalCatalog::categories() as $category) {
        $article = seedJournalCategoryArticles($category['id'], 1)[0];
        $excerpt = Str::of($article->introduction)->stripTags()->squish()->toString();

        $response = $this->get(route('journal.category', $category['id']));

        $response->assertSee(Str::limit($excerpt, 80), false);
        $response->assertDontSee($excerpt, false);
    }
});
