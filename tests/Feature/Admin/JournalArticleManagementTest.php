<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

function validJournalArticlePayload(int $categoryId, array $overrides = []): array
{
    return array_merge([
        'category_id' => $categoryId,
        'title' => 'Chapters Over a Slow Train',
        'introduction' => 'An introduction paragraph.',
        'featured_image' => UploadedFile::fake()->image('featured.jpg', 1200, 900),
        'editors_note' => "Editor's note body.",
        'authors_note' => "Author's note body.",
        'sections' => [
            [
                'title' => 'First Section',
                'media_type' => 'image',
                'content' => 'Section body copy.',
                'image_position' => 'center',
                'gallery' => [
                    ['image' => UploadedFile::fake()->image('gallery-1.jpg'), 'caption' => 'A caption.'],
                ],
            ],
        ],
        'faqs' => [
            ['question' => 'What is this about?', 'answer' => 'A slow travel story.'],
        ],
    ], $overrides);
}

test('super admin can view the journals list', function () {
    $user = User::factory()->superAdmin()->create();
    $article = Article::factory()->journal()->create(['title' => 'Letters from a Slow Train']);

    $this->actingAs($user)
        ->get(route('admin.journals.index'))
        ->assertOk()
        ->assertSee($article->title);
});

test('a user without view-journals cannot view the list', function () {
    $user = userWithPermission('view-features');

    $this->actingAs($user)
        ->get(route('admin.journals.index'))
        ->assertForbidden();
});

test('super admin created journal article is active immediately and auto-approved', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);

    $this->actingAs($user)->post(
        route('admin.journals.store'),
        validJournalArticlePayload($category->id),
    )->assertRedirect(route('admin.journals.index'));

    $article = Article::where('title', 'Chapters Over a Slow Train')->firstOrFail();

    expect($article->type)->toBe('journals');
    expect($article->status)->toBe('active');
    expect($article->sections)->toHaveCount(1);
    expect($article->sections->first()->galleryImages)->toHaveCount(1);
});

test('a non-super-admin created journal article is pending', function () {
    $user = userWithPermission(['view-journals', 'create-journals']);
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);

    $this->actingAs($user)->post(route('admin.journals.store'), validJournalArticlePayload($category->id));

    expect(Article::where('title', 'Chapters Over a Slow Train')->firstOrFail()->status)->toBe('pending');
});

test('a journals url cannot manage a feature article', function () {
    $user = userWithPermission(['view-journals', 'edit-journals']);
    $featureArticle = Article::factory()->feature()->create();

    $this->actingAs($user)
        ->get(route('admin.journals.edit', $featureArticle))
        ->assertNotFound();
});

test('a user with delete-journals can delete a journal article and its images', function () {
    $user = userWithPermission(['view-journals', 'create-journals', 'delete-journals']);
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);

    $this->actingAs($user)->post(route('admin.journals.store'), validJournalArticlePayload($category->id));
    $article = Article::where('title', 'Chapters Over a Slow Train')->firstOrFail();

    $this->actingAs($user)
        ->delete(route('admin.journals.destroy', $article))
        ->assertRedirect(route('admin.journals.index'));

    $this->assertDatabaseMissing('articles', ['id' => $article->id]);
});
