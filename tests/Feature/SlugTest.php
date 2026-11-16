<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Poem;
use App\Models\SlugRedirect;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

function journalCategory(string $slug = 'worth-knowing'): Category
{
    return Category::query()->ofType(Category::TYPE_JOURNAL)->where('slug', $slug)->firstOrFail();
}

function slugArticlePayload(int $categoryId, array $overrides = []): array
{
    return array_merge([
        'category_id' => $categoryId,
        'title' => 'Chapters Over a Slow Train',
        'introduction' => 'An introduction paragraph.',
        'featured_image' => UploadedFile::fake()->image('featured.jpg', 1200, 900),
        'editors_note' => "Editor's note body.",
        'authors_note' => "Author's note body.",
    ], $overrides);
}

function slugPoemPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'The Weight of Rain',
        'body' => 'It rained the day you left.',
        'featured_image' => UploadedFile::fake()->image('rain.jpg', 1200, 900),
    ], $overrides);
}

// --- Admin: articles -------------------------------------------------------

test('an article created without a slug gets one from its title', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.journals.store'), slugArticlePayload(journalCategory()->id))
        ->assertRedirect();

    expect(Article::sole()->slug)->toBe('chapters-over-a-slow-train');
});

test('an article can be created with a custom slug', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.journals.store'), slugArticlePayload(journalCategory()->id, ['slug' => 'slow-train']))
        ->assertRedirect();

    expect(Article::sole()->slug)->toBe('slow-train');
});

test('an article slug must be url-safe and unique within its category', function () {
    $category = journalCategory();
    Article::factory()->journal()->create(['category_id' => $category->id, 'slug' => 'taken']);
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->post(route('admin.journals.store'), slugArticlePayload($category->id, ['slug' => 'Not A Slug']))
        ->assertSessionHasErrors('slug');

    $this->actingAs($admin)
        ->post(route('admin.journals.store'), slugArticlePayload($category->id, ['slug' => 'taken']))
        ->assertSessionHasErrors('slug');

    // The same slug is fine in a different category.
    $this->actingAs($admin)
        ->post(route('admin.journals.store'), slugArticlePayload(journalCategory('the-bigger-picture')->id, ['slug' => 'taken']))
        ->assertSessionHasNoErrors();
});

test('editing an article title keeps its slug', function () {
    $article = Article::factory()->journal()->active()->create(['category_id' => journalCategory()->id, 'slug' => 'original-slug']);

    $this->actingAs(User::factory()->superAdmin()->create())
        ->put(route('admin.journals.update', $article), slugArticlePayload($article->category_id, [
            'title' => 'A Completely New Title',
            'slug' => 'original-slug',
            'featured_image' => null,
        ]))
        ->assertSessionHasNoErrors();

    expect($article->fresh()->slug)->toBe('original-slug');
    expect(SlugRedirect::count())->toBe(0);
});

test('clearing an article slug regenerates it from the title', function () {
    $article = Article::factory()->journal()->active()->create(['category_id' => journalCategory()->id, 'slug' => 'original-slug']);

    $this->actingAs(User::factory()->superAdmin()->create())
        ->put(route('admin.journals.update', $article), slugArticlePayload($article->category_id, [
            'title' => 'A Completely New Title',
            'slug' => '',
            'featured_image' => null,
        ]))
        ->assertSessionHasNoErrors();

    expect($article->fresh()->slug)->toBe('a-completely-new-title');
});

// --- Public: article redirects --------------------------------------------

test('an article old url redirects permanently to its new slug', function () {
    $article = Article::factory()->journal()->active()->create(['category_id' => journalCategory()->id, 'slug' => 'old-slug']);

    $article->update(['slug' => 'new-slug']);

    $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => 'old-slug']))
        ->assertRedirect(route('journal.article', ['category' => 'worth-knowing', 'article' => 'new-slug']))
        ->assertStatus(301);

    $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => 'new-slug']))
        ->assertSuccessful();
});

test('an article renamed twice redirects both old urls straight to the current one', function () {
    $article = Article::factory()->journal()->active()->create(['category_id' => journalCategory()->id, 'slug' => 'first']);

    $article->update(['slug' => 'second']);
    $article->update(['slug' => 'third']);

    foreach (['first', 'second'] as $old) {
        $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => $old]))
            ->assertRedirect(route('journal.article', ['category' => 'worth-knowing', 'article' => 'third']));
    }
});

test('an article moved to another category redirects from its old category url', function () {
    $article = Article::factory()->journal()->active()->create(['category_id' => journalCategory()->id, 'slug' => 'moving']);

    $article->update(['category_id' => journalCategory('the-bigger-picture')->id]);

    $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => 'moving']))
        ->assertRedirect(route('journal.article', ['category' => 'the-bigger-picture', 'article' => 'moving']))
        ->assertStatus(301);
});

test('a feature article old url redirects too', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'on-a-budget')->firstOrFail();
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id, 'slug' => 'old-feature']);

    $article->update(['slug' => 'new-feature']);

    $this->get(route('features.article', ['category' => 'on-a-budget', 'article' => 'old-feature']))
        ->assertRedirect(route('features.article', ['category' => 'on-a-budget', 'article' => 'new-feature']))
        ->assertStatus(301);
});

test('an old url 404s when the article is no longer active', function () {
    $article = Article::factory()->journal()->active()->create(['category_id' => journalCategory()->id, 'slug' => 'old-slug']);

    $article->update(['slug' => 'new-slug', 'status' => Article::STATUS_INACTIVE]);

    $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => 'old-slug']))
        ->assertNotFound();
});

test('a live article wins over an old redirect for the same url', function () {
    $category = journalCategory();
    $moved = Article::factory()->journal()->active()->create(['category_id' => $category->id, 'slug' => 'shared']);
    $moved->update(['slug' => 'moved-away']);

    $newcomer = Article::factory()->journal()->active()->create(['category_id' => $category->id, 'slug' => 'shared', 'title' => 'The Newcomer']);

    $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => 'shared']))
        ->assertSuccessful()
        ->assertSee(e($newcomer->title), false);
});

test('taking back a previous slug removes its redirect', function () {
    $article = Article::factory()->journal()->active()->create(['category_id' => journalCategory()->id, 'slug' => 'original']);

    $article->update(['slug' => 'temporary']);
    $article->update(['slug' => 'original']);

    expect($article->slugRedirects()->pluck('slug')->all())->toBe(['temporary']);
});

test('deleting an article removes its redirects', function () {
    $article = Article::factory()->journal()->active()->create(['category_id' => journalCategory()->id, 'slug' => 'old-slug']);
    $article->update(['slug' => 'new-slug']);

    $article->delete();

    expect(SlugRedirect::count())->toBe(0);
});

// --- Poems -------------------------------------------------------------------

test('a poem can be created with a custom slug', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.poetry.store'), slugPoemPayload(['slug' => 'rain']))
        ->assertRedirect(route('admin.poetry.index'));

    expect(Poem::sole()->slug)->toBe('rain');
});

test('a poem slug must be unique', function () {
    Poem::factory()->create(['slug' => 'rain']);

    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.poetry.store'), slugPoemPayload(['slug' => 'rain']))
        ->assertSessionHasErrors('slug');
});

test('editing a poem title keeps its slug, and changing the slug redirects the old url', function () {
    $poem = Poem::factory()->create(['slug' => 'the-weight-of-rain', 'status' => Poem::STATUS_ACTIVE]);
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->put(route('admin.poetry.update', $poem), ['title' => 'A New Title', 'slug' => 'the-weight-of-rain', 'body' => 'Body.'])
        ->assertSessionHasNoErrors();

    expect($poem->fresh()->slug)->toBe('the-weight-of-rain');

    $this->actingAs($admin)
        ->put(route('admin.poetry.update', $poem), ['title' => 'A New Title', 'slug' => 'rain', 'body' => 'Body.'])
        ->assertSessionHasNoErrors();

    $this->get(route('poetry.show', 'the-weight-of-rain'))
        ->assertRedirect(route('poetry.show', 'rain'))
        ->assertStatus(301);
});

// --- Never-empty slugs -------------------------------------------------------

test('a title with nothing url-safe falls back to a generic, unique slug', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)->post(route('admin.poetry.store'), slugPoemPayload(['title' => '☕☕']))->assertSessionHasNoErrors();
    $this->actingAs($admin)->post(route('admin.poetry.store'), slugPoemPayload(['title' => '!!! ???']))->assertSessionHasNoErrors();

    expect(Poem::orderBy('id')->pluck('slug')->all())->toBe(['poem', 'poem-2']);

    $this->actingAs($admin)
        ->post(route('admin.journals.store'), slugArticlePayload(journalCategory()->id, ['title' => '☕']))
        ->assertSessionHasNoErrors();

    expect(Article::sole()->slug)->toBe('article');
});

test('clearing the slug of an emoji-only title still saves a non-empty slug', function () {
    $poem = Poem::factory()->create(['title' => 'Old', 'slug' => 'old']);

    $this->actingAs(User::factory()->superAdmin()->create())
        ->put(route('admin.poetry.update', $poem), ['title' => '☕', 'slug' => '', 'body' => 'Body.'])
        ->assertSessionHasNoErrors();

    expect($poem->fresh()->slug)->toBe('poem');
});

test('a slug is filled in whenever a record is saved without one, from any code path', function () {
    $article = Article::factory()->journal()->create(['title' => 'Made in Code', 'slug' => null]);
    expect($article->slug)->toBe('made-in-code');

    $article->update(['slug' => '   ']);
    expect($article->fresh()->slug)->toBe('made-in-code');

    $poem = Poem::factory()->create(['title' => 'Also in Code', 'slug' => '']);
    expect($poem->slug)->toBe('also-in-code');
});

test('non-latin titles get a transliterated slug', function () {
    $poem = Poem::factory()->create(['title' => 'কফি ও কলা', 'slug' => null]);

    expect($poem->slug)->not->toBeEmpty()->toMatch('/^[a-z0-9]+(-[a-z0-9]+)*$/');
});
