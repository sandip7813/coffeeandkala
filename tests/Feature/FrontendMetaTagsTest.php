<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Meta;
use App\Models\Poem;

test('the home page shows its default meta description and keywords when nothing is set in the admin panel', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('COFFEE &amp; KALA — Editorial Journal', false);
    $response->assertSee('name="description" content="Coffee &amp; Kala is an editorial journal', false);
    $response->assertSee('name="keywords" content="Coffee &amp; Kala, editorial journal', false);
});

test('the home page shows the admin-set meta title, description, and keywords once saved', function () {
    Meta::forPage('home')->update([
        'title' => 'Custom Home Title',
        'description' => 'A custom home description.',
        'keywords' => 'custom, home, keywords',
    ]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('<title>Custom Home Title</title>', false);
    $response->assertSee('name="description" content="A custom home description."', false);
    $response->assertSee('name="keywords" content="custom, home, keywords"', false);
});

test('the poetry index page falls back to its default meta tags', function () {
    $response = $this->get(route('poetry'));

    $response->assertSuccessful();
    $response->assertSee('The Poetry Collection — Coffee &amp; Kala', false);
    $response->assertSee('name="description" content="A home in every poem', false);
});

test('a feature article page uses the article\'s own meta once set, falling back to its excerpt otherwise', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $article = Article::factory()->feature()->active()->create([
        'category_id' => $category->id,
        'title' => 'A Coastal Story',
        'introduction' => 'A quiet dispatch from the coast.',
    ]);

    $response = $this->get(route('features.article', ['category' => $category->slug, 'article' => $article->slug]));
    $response->assertSuccessful();
    $response->assertSee('name="description" content="A quiet dispatch from the coast."', false);

    $article->meta()->firstOrCreate([])->update(['description' => 'A custom article description.']);

    $response = $this->get(route('features.article', ['category' => $category->slug, 'article' => $article->slug]));
    $response->assertSuccessful();
    $response->assertSee('name="description" content="A custom article description."', false);
});

test('a poem page uses the poem\'s own meta once set, falling back to its excerpt otherwise', function () {
    $poem = Poem::factory()->active()->create(['title' => 'A Quiet Verse', 'body' => "Line one of the poem.\nLine two."]);

    $response = $this->get(route('poetry.show', $poem->slug));
    $response->assertSuccessful();
    $response->assertSee('name="description" content="Line one of the poem.', false);

    $poem->meta()->firstOrCreate([])->update(['description' => 'A custom poem description.']);

    $response = $this->get(route('poetry.show', $poem->slug));
    $response->assertSuccessful();
    $response->assertSee('name="description" content="A custom poem description."', false);
});
