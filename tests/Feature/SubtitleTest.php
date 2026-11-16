<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Poem;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

function subtitleCategory(): Category
{
    return Category::query()->ofType(Category::TYPE_JOURNAL)->where('slug', 'worth-knowing')->firstOrFail();
}

function subtitleArticlePayload(array $overrides = []): array
{
    return array_merge([
        'category_id' => subtitleCategory()->id,
        'title' => 'A Slow Walk',
        'subtitle' => 'Through the spice markets of Old Delhi',
        'introduction' => '<p>Intro.</p>',
        'featured_image' => UploadedFile::fake()->image('featured.jpg', 1200, 900),
        'editors_note' => '<p>Editor.</p>',
        'authors_note' => '<p>Author.</p>',
        'sections' => [
            ['title' => 'The First Lane', 'subtitle' => 'Where the cardamom sellers gather', 'content' => '<p>Body.</p>'],
        ],
    ], $overrides);
}

test('an article and its sections save their subtitles', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.journals.store'), subtitleArticlePayload())
        ->assertSessionHasNoErrors();

    $article = Article::sole();
    expect($article->subtitle)->toBe('Through the spice markets of Old Delhi');
    expect($article->sections()->sole()->subtitle)->toBe('Where the cardamom sellers gather');
});

test('subtitles are optional and can be cleared', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->post(route('admin.journals.store'), subtitleArticlePayload(['subtitle' => '', 'sections' => [['title' => 'Plain', 'content' => '<p>x</p>']]]))
        ->assertSessionHasNoErrors();

    $article = Article::sole();
    expect($article->subtitle)->toBeNull();
    expect($article->sections()->sole()->subtitle)->toBeNull();
});

test('a subtitle is limited to 200 characters', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.journals.store'), subtitleArticlePayload(['subtitle' => str_repeat('a', 201)]))
        ->assertSessionHasErrors('subtitle');
});

test('the article page shows the article and section subtitles', function () {
    $article = Article::factory()->journal()->active()->create([
        'category_id' => subtitleCategory()->id,
        'subtitle' => 'Through the spice markets of Old Delhi',
    ]);
    $article->sections()->create(['title' => 'The First Lane', 'subtitle' => 'Where the cardamom sellers gather', 'content' => '<p>Body.</p>', 'sort_order' => 0]);

    $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => $article->slug]))
        ->assertOk()
        ->assertSee('<p class="article-subtitle">Through the spice markets of Old Delhi</p>', false)
        ->assertSee('Where the cardamom sellers gather');
});

test('the article page shows no subtitle markup when there is none', function () {
    $article = Article::factory()->journal()->active()->create(['category_id' => subtitleCategory()->id]);

    $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => $article->slug]))
        ->assertOk()
        ->assertDontSee('article-subtitle', false);
});

test('a poem saves its subtitle and shows it on the poem page', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.poetry.store'), [
            'title' => 'The Weight of Rain',
            'subtitle' => 'For my mother',
            'body' => 'It rained the day you left.',
            'featured_image' => UploadedFile::fake()->image('rain.jpg', 900, 900),
        ])
        ->assertSessionHasNoErrors();

    $poem = Poem::sole();
    expect($poem->subtitle)->toBe('For my mother');

    $this->get(route('poetry.show', $poem->slug))
        ->assertOk()
        ->assertSee('<p class="poetry-book-poem-subtitle">For my mother</p>', false);
});

test('subtitles with quotes and ampersands are escaped exactly once in the edit forms', function () {
    $admin = User::factory()->superAdmin()->create();
    $poem = Poem::factory()->create(['subtitle' => 'A "quoted" line & more']);
    $article = Article::factory()->journal()->create(['category_id' => subtitleCategory()->id, 'subtitle' => 'A "quoted" line & more']);

    foreach ([route('admin.poetry.edit', $poem), route('admin.journals.edit', $article)] as $url) {
        $this->actingAs($admin)
            ->get($url)
            ->assertOk()
            ->assertSee('value="A &quot;quoted&quot; line &amp; more"', false);
    }
});

test('unsaved subtitles show in previews', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.journals.preview.form'), subtitleArticlePayload(['featured_image' => null]))
        ->assertOk()
        ->assertSee('Through the spice markets of Old Delhi')
        ->assertSee('Where the cardamom sellers gather');

    expect(Article::count())->toBe(0);
});
