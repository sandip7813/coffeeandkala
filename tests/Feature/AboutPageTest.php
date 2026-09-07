<?php

use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\Category;

test('the about page returns a successful response', function () {
    $response = $this->get(route('about'));

    $response->assertSuccessful();
    $response->assertSee('Our Story', false);
    $response->assertSee('Coffee &amp; Kala', false);
    $response->assertSee('images/about/banner.png', false);
    $response->assertSee('aboutBannerZoom', false);
    $response->assertSee('Show full banner image', false);
    $response->assertSee('about-banner-divider', false);
    $response->assertDontSee('aboutBannerLightbox', false);
    $response->assertDontSee('editorialSidebar', false);
    $response->assertDontSee('toggleLeftDrawer', false);
    $response->assertSee('about-topbar', false);
    $response->assertSee('about-reveal', false);
});

test('the about page renders no content sections when Our Story has none yet', function () {
    $response = $this->get(route('about'));

    $response->assertSuccessful();
    $response->assertDontSee('article-sections', false);
});

test('the about page renders Our Story\'s admin-authored sections, same as a Feature/Journal article body', function () {
    $category = Category::create(['title' => 'Our Story', 'slug' => 'our-story', 'type' => Category::TYPE_OUR_STORY, 'status' => true, 'sort_order' => 0]);
    $article = Article::factory()->create([
        'type' => Article::TYPE_OUR_STORY,
        'category_id' => $category->id,
        'title' => 'Our Story',
        'slug' => 'our-story',
        'status' => Article::STATUS_ACTIVE,
    ]);
    ArticleSection::create([
        'article_id' => $article->id,
        'sort_order' => 0,
        'title' => 'How It Began',
        'media_type' => null,
        'content' => '<p>The founder\'s own words.</p>',
        'is_active' => true,
    ]);
    ArticleSection::create([
        'article_id' => $article->id,
        'sort_order' => 1,
        'title' => 'A Hidden Chapter',
        'media_type' => null,
        'content' => '<p>Not ready yet.</p>',
        'is_active' => false,
    ]);

    $response = $this->get(route('about'));

    $response->assertSuccessful();
    $response->assertSee('article-sections', false);
    $response->assertSee('How It Began', false);
    $response->assertSee('The founder\'s own words.', false);
    $response->assertDontSee('A Hidden Chapter', false);
    $response->assertDontSee('Not ready yet.', false);
});

test('a pending Our Story article never reaches the frontend', function () {
    $category = Category::create(['title' => 'Our Story', 'slug' => 'our-story', 'type' => Category::TYPE_OUR_STORY, 'status' => true, 'sort_order' => 0]);
    $article = Article::factory()->create([
        'type' => Article::TYPE_OUR_STORY,
        'category_id' => $category->id,
        'status' => Article::STATUS_PENDING,
    ]);
    ArticleSection::create([
        'article_id' => $article->id,
        'sort_order' => 0,
        'title' => 'Not Yet Published',
        'media_type' => null,
        'content' => '<p>Still pending.</p>',
        'is_active' => true,
    ]);

    $response = $this->get(route('about'));

    $response->assertSuccessful();
    $response->assertDontSee('Not Yet Published', false);
});

test('our story links to the about page from the home page', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee(route('about', absolute: false), false);
});
