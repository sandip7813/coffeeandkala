<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\HomeSectionArticle;
use App\Models\HomeSectionMedia;
use App\Models\MediaFile;

/**
 * @return array{feature: Article, journal: Article}
 */
function seedHomeSectionArticles(): array
{
    $featureCategory = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $journalCategory = Category::query()->ofType(Category::TYPE_JOURNAL)->where('slug', 'worth-knowing')->firstOrFail();

    return [
        'feature' => Article::factory()->feature()->active()->create(['category_id' => $featureCategory->id, 'title' => 'A Featured Story']),
        'journal' => Article::factory()->journal()->active()->create(['category_id' => $journalCategory->id, 'title' => 'A Journal Dispatch']),
    ];
}

test('a home section stays hidden when nothing has been picked for it', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('Latest Pieces', false);
    $response->assertDontSee('The Selection', false);
    $response->assertDontSee('data-visual-feature', false);
    $response->assertDontSee('data-journal-feature', false);
});

test('a home section shows its picked article once one exists', function () {
    ['feature' => $feature] = seedHomeSectionArticles();

    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_LATEST_PIECES, 'article_id' => $feature->id, 'sort_order' => 0]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Latest Pieces', false);
    $response->assertSee('A Featured Story', false);
});

test('a home section with only one pick hides its slider arrows', function () {
    ['feature' => $feature] = seedHomeSectionArticles();

    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_LATEST_PIECES, 'article_id' => $feature->id, 'sort_order' => 0]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('hero-banner-controls', false);
    $response->assertDontSee('hero-banner-progress', false);
});

test('a home section with more than one pick shows its slider arrows, in the picked order', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $first = Article::factory()->feature()->active()->create(['category_id' => $category->id, 'title' => 'First Pick']);
    $second = Article::factory()->feature()->active()->create(['category_id' => $category->id, 'title' => 'Second Pick']);

    // Deliberately picked out of creation order — sort_order drives display order.
    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_LATEST_PIECES, 'article_id' => $second->id, 'sort_order' => 0]);
    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_LATEST_PIECES, 'article_id' => $first->id, 'sort_order' => 1]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('hero-banner-controls', false);

    $html = $response->getContent();
    expect(strpos($html, 'Second Pick'))->toBeLessThan(strpos($html, 'First Pick'));
});

test('a pick whose article has since been deactivated no longer shows on the homepage', function () {
    ['feature' => $feature] = seedHomeSectionArticles();

    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_LATEST_PIECES, 'article_id' => $feature->id, 'sort_order' => 0]);
    $feature->update(['status' => Article::STATUS_INACTIVE]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('Latest Pieces', false);
    $response->assertDontSee('A Featured Story', false);
});

test('the left sidebar drops the nav item for a section that has nothing picked', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('data-target="sec-03"', false);
    $response->assertDontSee('data-target="sec-04"', false);
    $response->assertDontSee('data-target="sec-05"', false);
    $response->assertDontSee('data-target="sec-06"', false);
    $response->assertDontSee('data-target="sec-07"', false);
    $response->assertDontSee('data-target="sec-08"', false);
    // Always-present sections stay in the sidebar.
    $response->assertSee('data-target="sec-01"', false);
    $response->assertSee('data-target="sec-02"', false);
    $response->assertSee('data-target="sec-09"', false);
});

test('the left sidebar keeps the nav item for a section once something is picked', function () {
    ['feature' => $feature] = seedHomeSectionArticles();
    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_LATEST_PIECES, 'article_id' => $feature->id, 'sort_order' => 0]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('data-target="sec-03"', false);
    $response->assertDontSee('data-target="sec-04"', false);
});

test('the homepage Gallery and Studio carousels stay hidden until an image is picked', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('section-gallery-story', false);
    $response->assertDontSee('section-visual-poetry', false);
});

test('the homepage Gallery and Studio carousels show their picked images', function () {
    $gallery = MediaFile::factory()->ofType('gallery')->active()->create(['title' => 'A Picked Plate']);
    $studio = MediaFile::factory()->ofType('studio')->active()->create(['title' => 'A Picked Work']);

    HomeSectionMedia::create(['section' => HomeSectionMedia::SECTION_GALLERY, 'media_id' => $gallery->id, 'sort_order' => 0]);
    HomeSectionMedia::create(['section' => HomeSectionMedia::SECTION_STUDIO, 'media_id' => $studio->id, 'sort_order' => 0]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('section-gallery-story', false);
    $response->assertSee('A Picked Plate', false);
    $response->assertSee('section-visual-poetry', false);
    $response->assertSee('A Picked Work', false);
});

test('the features and journal home sections render their own picks independently', function () {
    ['feature' => $feature, 'journal' => $journal] = seedHomeSectionArticles();

    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_FEATURES, 'article_id' => $feature->id, 'sort_order' => 0]);
    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_JOURNAL, 'article_id' => $journal->id, 'sort_order' => 0]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('data-visual-feature', false);
    $response->assertSee('A Featured Story', false);
    $response->assertSee('data-journal-feature', false);
    $response->assertSee('A Journal Dispatch', false);
});
