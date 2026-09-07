<?php

use App\Models\Article;
use App\Models\ArticleFaq;
use App\Models\ArticleSection;
use App\Models\Category;

test('a feature article detail page returns a successful response', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $article = Article::factory()->feature()->active()->create([
        'category_id' => $category->id,
        'title' => 'A Real Feature Article',
    ]);
    ArticleFaq::create(['article_id' => $article->id, 'question' => 'Is this real?', 'answer' => 'Yes.', 'sort_order' => 0]);

    $response = $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => $article->slug]));

    $response->assertSuccessful();
    $response->assertSee(e($article->title), false);
    $response->assertSee('features-theme--on-a-budget', false);
    $response->assertSee('Table of Contents', false);
    $response->assertSee('Editor&rsquo;s Note', false);
    $response->assertSee('Frequently Asked Questions', false);
    $response->assertSee('Author&rsquo;s Note', false);
    $response->assertSee('Explore the Sections', false);
    // No other published articles exist yet, so "Recently Published" has
    // nothing to show and stays hidden — see the dedicated test below.
    $response->assertDontSee('Recently Published', false);
});

test('"Recently Published" only shows when another article actually exists', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id, 'title' => 'Main Article']);

    $withoutOthers = $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => $article->slug]));
    $withoutOthers->assertDontSee('Recently Published', false);

    Article::factory()->feature()->active()->create(['category_id' => $category->id, 'title' => 'Another Article']);

    $withOthers = $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => $article->slug]));
    $withOthers->assertSee('Recently Published', false);
    $withOthers->assertSee('Another Article', false);
});

test('a journal entry detail page returns a successful response', function () {
    $category = Category::query()->ofType(Category::TYPE_JOURNAL)->where('slug', 'worth-knowing')->firstOrFail();
    $article = Article::factory()->journal()->active()->create([
        'category_id' => $category->id,
        'title' => 'A Real Journal Entry',
    ]);
    ArticleFaq::create(['article_id' => $article->id, 'question' => 'Is this real?', 'answer' => 'Yes.', 'sort_order' => 0]);

    $response = $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => $article->slug]));

    $response->assertSuccessful();
    $response->assertSee(e($article->title), false);
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

test('the FAQ heading is hidden when the article has no FAQs', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $response = $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => $article->slug]));

    $response->assertSuccessful();
    $response->assertDontSee('Frequently Asked Questions', false);
});

test('a pending or draft article is not reachable on the frontend', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();

    $pending = Article::factory()->feature()->create(['category_id' => $category->id, 'title' => 'Pending Article']);
    $draft = Article::factory()->feature()->create(['category_id' => $category->id, 'title' => 'Draft Article', 'status' => 'draft']);

    $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => $pending->slug]))->assertNotFound();
    $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => $draft->slug]))->assertNotFound();
});

test('admin-authored inline styling (from Quill or pasted content) never reaches the frontend', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $article = Article::factory()->feature()->active()->create([
        'category_id' => $category->id,
        'introduction' => '<p style="background-color:white;font-family:Calibri;color:red;" class="MsoNormal">A pasted paragraph.</p>',
    ]);

    ArticleSection::create([
        'article_id' => $article->id,
        'sort_order' => 0,
        'title' => 'A Section',
        'media_type' => null,
        'content' => '<p style="background:white;font-family:Arial;">Section body with pasted styling.</p>',
        'is_active' => true,
    ]);

    $response = $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => $article->slug]));

    $response->assertSuccessful();
    $response->assertDontSee('background-color:white', false);
    $response->assertDontSee('font-family:Calibri', false);
    $response->assertDontSee('MsoNormal', false);
    $response->assertDontSee('background:white', false);
    $response->assertDontSee('font-family:Arial', false);
    $response->assertSee('A pasted paragraph.', false);
    $response->assertSee('Section body with pasted styling.', false);
});

test('the detail page renders real sections, respecting each one\'s Active/Inactive flag', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    ArticleSection::create([
        'article_id' => $article->id,
        'sort_order' => 0,
        'title' => 'A Visible Section',
        'media_type' => null,
        'content' => '<p>Visible section content.</p>',
        'is_active' => true,
    ]);
    ArticleSection::create([
        'article_id' => $article->id,
        'sort_order' => 1,
        'title' => 'A Hidden Section',
        'media_type' => null,
        'content' => '<p>Hidden section content.</p>',
        'is_active' => false,
    ]);

    $response = $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => $article->slug]));

    $response->assertSuccessful();
    $response->assertSee('A Visible Section', false);
    $response->assertSee('Visible section content.', false);
    $response->assertDontSee('A Hidden Section', false);
    $response->assertDontSee('Hidden section content.', false);
});

test('a youtube section shows a clickable thumbnail instead of an autoplaying iframe', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    ArticleSection::create([
        'article_id' => $article->id,
        'sort_order' => 0,
        'title' => 'A Video Section',
        'media_type' => 'video',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'youtube_position' => 'left',
        'video_companion_type' => 'none',
        'is_active' => true,
    ]);

    $response = $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => $article->slug]));

    $response->assertSuccessful();
    $response->assertDontSee('<iframe', false);
    $response->assertSee('img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg', false);
    $response->assertSee('data-fancybox="article-video"', false);
    $response->assertSee('data-type="iframe"', false);
    $response->assertSee('youtube.com/embed/dQw4w9WgXcQ?autoplay=1', false);
    $response->assertSee('article-section-video-play', false);
});

test('a youtube section title spans the full width instead of sitting in the text column', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    ArticleSection::create([
        'article_id' => $article->id,
        'sort_order' => 0,
        'title' => 'A Full-Width Video Title',
        'media_type' => 'video',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'youtube_position' => 'left',
        'video_companion_type' => 'none',
        'is_active' => true,
    ]);

    $response = $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => $article->slug]));

    $response->assertSuccessful();
    $response->assertSee('<h2 class="article-section-title-full">A Full-Width Video Title</h2>', false);
    // It must not be duplicated inside the two-column body as a plain <h2>.
    $response->assertDontSee('<h2>A Full-Width Video Title</h2>', false);
});

test('a FAQ answer\'s line breaks are preserved as <br> tags', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);
    ArticleFaq::create([
        'article_id' => $article->id,
        'question' => 'Multi-line question?',
        'answer' => "First line.\nSecond line.",
        'sort_order' => 0,
    ]);

    $response = $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => $article->slug]));

    $response->assertSuccessful();
    $response->assertSee("First line.<br />\nSecond line.", false);
});
