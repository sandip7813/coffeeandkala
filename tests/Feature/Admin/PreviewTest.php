<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Poem;
use App\Models\User;
use App\Support\Preview\PreviewUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

function previewCategory(string $type = Category::TYPE_JOURNAL, string $slug = 'worth-knowing'): Category
{
    return Category::query()->ofType($type)->where('slug', $slug)->firstOrFail();
}

function previewJournal(array $attributes = []): Article
{
    return Article::factory()->journal()->create([
        'category_id' => previewCategory()->id,
        'title' => 'The Saved Title',
        'status' => Article::STATUS_DRAFT,
        ...$attributes,
    ]);
}

// --- Saved version ---------------------------------------------------------

test('a draft article can be previewed in the real page design, marked as an unindexed preview', function () {
    $article = previewJournal();

    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.journals.preview', $article))
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
        ->assertSee('The Saved Title')
        ->assertSee('jc-theme--worth-knowing', false)
        ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
        ->assertSee('Not published')
        ->assertSee('Saved version · Draft');
});

test('the public page still 404s for that same draft', function () {
    $article = previewJournal();

    $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => $article->slug]))->assertNotFound();
});

test('pending and inactive feature articles can be previewed too', function () {
    $category = previewCategory(Category::TYPE_FEATURE, 'on-a-budget');
    $admin = User::factory()->superAdmin()->create();

    foreach ([Article::STATUS_PENDING, Article::STATUS_INACTIVE] as $status) {
        $article = Article::factory()->feature()->create(['category_id' => $category->id, 'status' => $status]);

        $this->actingAs($admin)
            ->get(route('admin.features.preview', $article))
            ->assertOk()
            ->assertSee('features-theme--on-a-budget', false);
    }
});

test('an article can only be previewed through its own type', function () {
    $article = previewJournal();

    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.features.preview', $article))
        ->assertNotFound();
});

test('previewing needs create, edit or approve permission for that type', function () {
    $article = previewJournal();

    $this->actingAs(userWithPermission('view-journals'))
        ->get(route('admin.journals.preview', $article))
        ->assertForbidden();

    $this->actingAs(userWithPermission(['view-journals', 'create-journals']))
        ->get(route('admin.journals.preview', $article))
        ->assertOk();

    auth()->logout();
    $this->get(route('admin.journals.preview', $article))->assertRedirect(route('login'));
});

// --- Unsaved form ----------------------------------------------------------

test('a new article can be previewed from the form without saving anything', function () {
    $response = $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.journals.preview.form'), [
            'category_id' => previewCategory()->id,
            'title' => 'Not Saved Yet',
            'introduction' => '<p>An unsaved introduction.</p>',
            'editors_note' => '<p>Editor note.</p>',
            'authors_note' => '<p>Author note.</p>',
            'featured_image' => UploadedFile::fake()->image('featured.jpg', 1200, 900),
            'sections' => [
                ['title' => 'An Unsaved Section', 'media_type' => 'image', 'content' => '<p>Section body.</p>', 'image_position' => 'center',
                    'image' => UploadedFile::fake()->image('section.jpg', 800, 600)],
            ],
            'faqs' => [
                ['question' => 'Is this saved?', 'answer' => 'No.'],
                ['question' => '', 'answer' => ''],
            ],
        ]);

    $response->assertOk()
        ->assertSee('Not Saved Yet')
        ->assertSee('An unsaved introduction.')
        ->assertSee('An Unsaved Section')
        ->assertSee('Is this saved?')
        ->assertSee('Unsaved changes');

    expect(Article::count())->toBe(0);

    // Both picked images were stored temporarily, for the preview to show.
    $previewFiles = Storage::disk('public')->files(PreviewUploads::DIRECTORY);
    expect($previewFiles)->toHaveCount(2);
    $response->assertSee(Storage::disk('public')->url($previewFiles[0]), false);
});

test('unsaved edits to a published article can be previewed while the live article stays unchanged', function () {
    $article = previewJournal(['status' => Article::STATUS_ACTIVE, 'slug' => 'the-saved-title']);
    $kept = $article->sections()->create(['title' => 'Kept Section', 'content' => '<p>Kept.</p>', 'sort_order' => 0]);
    $article->sections()->create(['title' => 'Removed Section', 'content' => '<p>Removed.</p>', 'sort_order' => 1]);

    $this->actingAs(User::factory()->superAdmin()->create())
        ->put(route('admin.journals.preview.form', $article), [
            'category_id' => $article->category_id,
            'title' => 'An Edited Title',
            'slug' => 'the-saved-title',
            'introduction' => $article->introduction,
            'editors_note' => $article->editors_note,
            'authors_note' => $article->authors_note,
            'sections' => [
                ['id' => $kept->id, 'title' => 'Kept Section, Renamed', 'content' => '<p>Kept.</p>', 'is_active' => '1'],
            ],
            'meta_title' => 'Edited SEO Title',
        ])
        ->assertOk()
        ->assertSee('An Edited Title')
        ->assertSee('Kept Section, Renamed')
        ->assertDontSee('Removed Section')
        ->assertSee('<title>Edited SEO Title</title>', false);

    $article->refresh();
    expect($article->title)->toBe('The Saved Title');
    expect($article->sections()->count())->toBe(2);
    expect($kept->fresh()->title)->toBe('Kept Section');

    $this->get(route('journal.article', ['category' => 'worth-knowing', 'article' => 'the-saved-title']))
        ->assertOk()
        ->assertSee('The Saved Title')
        ->assertDontSee('An Edited Title');
});

test('the article forms show a Preview button and the list a Preview link', function () {
    $article = previewJournal();
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)->get(route('admin.journals.create'))
        ->assertSee('formaction="'.route('admin.journals.preview.form').'"', false);

    $this->actingAs($admin)->get(route('admin.journals.edit', $article))
        ->assertSee('formaction="'.route('admin.journals.preview.form', $article).'"', false);

    $this->actingAs($admin)->get(route('admin.journals.index'))
        ->assertSee(route('admin.journals.preview', $article), false);
});

// --- Poems -------------------------------------------------------------------

test('a pending poem can be previewed, and the public page still 404s for it', function () {
    $poem = Poem::factory()->create(['title' => 'A Pending Poem', 'slug' => 'a-pending-poem', 'status' => Poem::STATUS_PENDING]);

    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.poetry.preview', $poem))
        ->assertOk()
        ->assertSee('A Pending Poem')
        ->assertSee('Saved version · Pending');

    auth()->logout();
    $this->get(route('poetry.show', 'a-pending-poem'))->assertNotFound();
});

test('unsaved poem edits can be previewed without saving', function () {
    $poem = Poem::factory()->create(['title' => 'Saved Poem', 'body' => 'Saved line.', 'status' => Poem::STATUS_ACTIVE]);

    $this->actingAs(User::factory()->superAdmin()->create())
        ->put(route('admin.poetry.preview.form', $poem), ['title' => 'Edited Poem', 'body' => "An edited line.\n\nA second stanza."])
        ->assertOk()
        ->assertSee('Edited Poem')
        ->assertSee('An edited line.')
        ->assertSee('Unsaved changes');

    expect($poem->fresh()->title)->toBe('Saved Poem');
});

test('a brand-new poem can be previewed from the create form', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.poetry.preview.form'), [
            'title' => 'Fresh Poem',
            'body' => 'A first line.',
            'featured_image' => UploadedFile::fake()->image('poem.jpg', 900, 900),
        ])
        ->assertOk()
        ->assertSee('Fresh Poem');

    expect(Poem::count())->toBe(0);
});

test('poem previews need a poetry permission', function () {
    $poem = Poem::factory()->create();

    $this->actingAs(userWithPermission('view-poetry'))
        ->get(route('admin.poetry.preview', $poem))
        ->assertForbidden();
});

// --- Clean-up ----------------------------------------------------------------

test('old preview images are pruned, recent ones kept', function () {
    $disk = Storage::disk('public');
    $disk->put(PreviewUploads::DIRECTORY.'/old.jpg', 'x');
    $disk->put(PreviewUploads::DIRECTORY.'/new.jpg', 'x');
    touch($disk->path(PreviewUploads::DIRECTORY.'/old.jpg'), now()->subHours(7)->getTimestamp());

    $this->artisan('previews:prune')->assertSuccessful();

    expect($disk->files(PreviewUploads::DIRECTORY))->toBe([PreviewUploads::DIRECTORY.'/new.jpg']);
});
