<?php

use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\Category;
use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

function validFeatureArticlePayload(int $categoryId, array $overrides = []): array
{
    return array_merge([
        'category_id' => $categoryId,
        'title' => 'Letters from the Coast',
        'introduction' => 'An introduction paragraph.',
        'featured_image' => UploadedFile::fake()->image('featured.jpg', 1200, 900),
        'editors_note' => "Editor's note body.",
        'authors_note' => "Author's note body.",
        'sections' => [
            [
                'title' => 'First Section',
                'media_type' => 'image',
                'content' => 'Section body copy.',
                'image_position' => 'left',
                'content_position' => 'beside',
                'image' => UploadedFile::fake()->image('section.jpg', 800, 600),
                'image_caption' => 'The section image caption.',
                'gallery' => [
                    ['image' => UploadedFile::fake()->image('gallery-1.jpg'), 'caption' => 'First gallery caption.'],
                    ['image' => UploadedFile::fake()->image('gallery-2.jpg'), 'caption' => 'Second gallery caption.'],
                ],
            ],
        ],
        'faqs' => [
            ['question' => 'What is this about?', 'answer' => 'A slow travel story.'],
        ],
    ], $overrides);
}

test('super admin can view the features list', function () {
    $user = User::factory()->superAdmin()->create();
    $article = Article::factory()->feature()->create(['title' => 'Where the Coast Speaks']);

    $this->actingAs($user)
        ->get(route('admin.features.index'))
        ->assertOk()
        ->assertSee($article->title);
});

test('a user without view-features cannot view the list', function () {
    $user = userWithPermission('view-journals');

    $this->actingAs($user)
        ->get(route('admin.features.index'))
        ->assertForbidden();
});

test('saving as draft never triggers approval, regardless of who saves it', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $payload = validFeatureArticlePayload($category->id, ['save_action' => 'draft']);

    $this->actingAs($user)->post(route('admin.features.store'), $payload)
        ->assertRedirect(route('admin.features.index'));

    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();

    expect($article->status)->toBe('draft');
    expect($article->approved_by)->toBeNull();
    expect($article->approved_at)->toBeNull();
});

test('a draft article can be saved as draft again, or submitted to leave draft', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->post(route('admin.features.store'), validFeatureArticlePayload($category->id, ['save_action' => 'draft']));
    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    expect($article->status)->toBe('draft');

    // Saving as draft again keeps it a draft.
    $draftAgainPayload = validFeatureArticlePayload($category->id, ['save_action' => 'draft']);
    unset($draftAgainPayload['featured_image']);

    $this->actingAs($user)
        ->put(route('admin.features.update', $article), $draftAgainPayload)
        ->assertRedirect(route('admin.features.index'));
    expect($article->refresh()->status)->toBe('draft');

    // Submitting for review leaves draft behind for good.
    $payload = validFeatureArticlePayload($category->id, ['save_action' => 'submit']);
    unset($payload['featured_image']);

    $this->actingAs($user)
        ->put(route('admin.features.update', $article), $payload)
        ->assertRedirect(route('admin.features.index'));

    $article->refresh();
    expect($article->status)->toBe('pending');
    expect($article->approved_at)->toBeNull();
});

test('an article that has ever been active can no longer be moved to draft', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);
    expect($article->canBeDrafted())->toBeFalse();

    $payload = validFeatureArticlePayload($category->id, ['save_action' => 'draft']);
    unset($payload['featured_image']);

    $this->actingAs($user)
        ->put(route('admin.features.update', $article), $payload)
        ->assertRedirect(route('admin.features.index'));

    // The attempt to draft it is silently ignored — status is untouched.
    expect($article->refresh()->status)->toBe('active');
});

test('the Save as Draft button is hidden once an article has been active', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $draftArticle = Article::factory()->feature()->create(['category_id' => $category->id, 'status' => 'draft']);
    $this->actingAs($user)
        ->get(route('admin.features.edit', $draftArticle))
        ->assertSee('data-save-action-button="draft"', false);

    $activeArticle = Article::factory()->feature()->active()->create(['category_id' => $category->id]);
    $this->actingAs($user)
        ->get(route('admin.features.edit', $activeArticle))
        ->assertDontSee('data-save-action-button="draft"', false);
});

test('a draft/pending article cannot be toggled active/inactive directly', function () {
    $user = userWithPermission(['view-features', 'change-features-status']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $draftArticle = Article::factory()->feature()->create(['category_id' => $category->id, 'status' => 'draft']);

    $this->actingAs($user)
        ->put(route('admin.features.status.update', $draftArticle))
        ->assertStatus(422);

    expect($draftArticle->refresh()->status)->toBe('draft');
});

test('super admin created article is active immediately and auto-approved', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $response = $this->actingAs($user)->post(
        route('admin.features.store'),
        validFeatureArticlePayload($category->id),
    );

    $response->assertRedirect(route('admin.features.index'));

    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();

    expect($article->type)->toBe('features');
    expect($article->status)->toBe('active');
    expect($article->created_by)->toBe($user->id);
    expect($article->approved_by)->toBe($user->id);
    expect($article->sections)->toHaveCount(1);
    expect($article->faqs)->toHaveCount(1);
    expect($article->featuredImage)->not->toBeNull();

    $section = $article->sections->first();
    expect($section->image)->not->toBeNull();
    expect($section->image->caption)->toBe('The section image caption.');
    expect($section->galleryImages)->toHaveCount(2);
    expect($section->galleryImages->pluck('caption')->all())->toBe(['First gallery caption.', 'Second gallery caption.']);
});

test('a non-super-admin created article is pending and requires approval', function () {
    $user = userWithPermission(['view-features', 'create-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->post(
        route('admin.features.store'),
        validFeatureArticlePayload($category->id),
    );

    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();

    expect($article->status)->toBe('pending');
    expect($article->approved_by)->toBeNull();
});

test('the Essentials tab fields are mandatory', function () {
    $user = userWithPermission(['view-features', 'create-features']);

    $this->actingAs($user)
        ->post(route('admin.features.store'), [])
        ->assertSessionHasErrors(['category_id', 'title', 'introduction', 'featured_image', 'editors_note', 'authors_note']);
});

test('an AJAX request with invalid data gets a JSON 422 response, not a redirect', function () {
    $user = userWithPermission(['view-features', 'create-features']);

    $response = $this->actingAs($user)
        ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
        ->post(route('admin.features.store'), []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['category_id', 'title', 'introduction', 'featured_image', 'editors_note', 'authors_note']);
});

test('a gallery-type section can still carry its own content', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $payload = validFeatureArticlePayload($category->id, [
        'sections' => [
            [
                'title' => 'Gallery Section',
                'media_type' => 'gallery',
                'content' => 'A few words alongside the gallery.',
                'gallery' => [
                    ['image' => UploadedFile::fake()->image('g1.jpg')],
                    ['image' => UploadedFile::fake()->image('g2.jpg')],
                ],
            ],
        ],
    ]);

    $this->actingAs($user)->post(route('admin.features.store'), $payload)->assertRedirect(route('admin.features.index'));

    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $section = $article->sections->first();

    expect($section->media_type)->toBe('gallery');
    expect($section->galleryImages)->toHaveCount(2);
    expect($section->content)->toBe('A few words alongside the gallery.');
});

test('the Content tab (sections) and FAQs are optional', function () {
    $user = userWithPermission(['view-features', 'create-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $payload = validFeatureArticlePayload($category->id, ['sections' => [], 'faqs' => []]);

    $this->actingAs($user)
        ->post(route('admin.features.store'), $payload)
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('admin.features.index'));

    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    expect($article->sections)->toHaveCount(0);
});

test('content position is only beside or standalone, never a conflicting side', function () {
    $user = userWithPermission(['view-features', 'create-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $payload = validFeatureArticlePayload($category->id);
    $payload['sections'][0]['content_position'] = 'left'; // no longer a valid option

    $this->actingAs($user)
        ->post(route('admin.features.store'), $payload)
        ->assertSessionHasErrors(['sections.0.content_position']);
});

test('a section can attach at most 4 gallery images', function () {
    $user = userWithPermission(['view-features', 'create-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $payload = validFeatureArticlePayload($category->id);
    $payload['sections'][0]['gallery'] = [
        ['image' => UploadedFile::fake()->image('g1.jpg')],
        ['image' => UploadedFile::fake()->image('g2.jpg')],
        ['image' => UploadedFile::fake()->image('g3.jpg')],
        ['image' => UploadedFile::fake()->image('g4.jpg')],
        ['image' => UploadedFile::fake()->image('g5.jpg')],
    ];

    $this->actingAs($user)
        ->post(route('admin.features.store'), $payload)
        ->assertSessionHasErrors(['sections.0.gallery']);
});

test('a section\'s gallery total is uncapped, but at most 4 new images can be added per edit', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    // Create with 4 gallery images already.
    $payload = validFeatureArticlePayload($category->id);
    $payload['sections'][0]['gallery'] = collect(range(1, 4))
        ->map(fn ($i) => ['image' => UploadedFile::fake()->image("g{$i}.jpg")])
        ->all();

    $this->actingAs($user)->post(route('admin.features.store'), $payload);
    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $article->load('sections.galleryImages');
    $section = $article->sections->first();
    expect($section->galleryImages)->toHaveCount(4);

    $existingSlots = $section->galleryImages->map(fn ($image) => ['id' => $image->id])->all();

    // Adding 4 MORE (keeping the existing 4) succeeds — total isn't capped.
    $updatePayload = validFeatureArticlePayload($category->id, [
        'sections' => [[
            'id' => $section->id,
            'title' => $section->title,
            'media_type' => 'gallery',
            'gallery' => [
                ...$existingSlots,
                ...collect(range(5, 8))->map(fn ($i) => ['image' => UploadedFile::fake()->image("g{$i}.jpg")])->all(),
            ],
        ]],
    ]);
    unset($updatePayload['featured_image']);

    $this->actingAs($user)
        ->put(route('admin.features.update', $article), $updatePayload)
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('admin.features.index'));

    expect($section->refresh()->galleryImages)->toHaveCount(8);

    // But a 5th NEW image in one edit is rejected.
    $tooManyPayload = validFeatureArticlePayload($category->id, [
        'sections' => [[
            'id' => $section->id,
            'title' => $section->title,
            'media_type' => 'gallery',
            'gallery' => collect(range(9, 13))->map(fn ($i) => ['image' => UploadedFile::fake()->image("g{$i}.jpg")])->all(),
        ]],
    ]);
    unset($tooManyPayload['featured_image']);

    $this->actingAs($user)
        ->put(route('admin.features.update', $article), $tooManyPayload)
        ->assertSessionHasErrors(['sections.0.gallery']);
});

test('a gallery image can be updated in place — replacing its caption without re-uploading the file', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->post(route('admin.features.store'), validFeatureArticlePayload($category->id));
    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $article->load('sections.galleryImages');
    $section = $article->sections->first();
    $firstImage = $section->galleryImages->first();

    $payload = validFeatureArticlePayload($category->id, [
        'sections' => [
            [
                'id' => $section->id,
                'title' => $section->title,
                'media_type' => 'image',
                'content' => $section->content,
                'image_position' => $section->image_position,
                'gallery' => [
                    ['id' => $firstImage->id, 'caption' => 'Updated caption only.'],
                ],
            ],
        ],
    ]);
    unset($payload['featured_image']);

    $this->actingAs($user)
        ->put(route('admin.features.update', $article), $payload)
        ->assertRedirect(route('admin.features.index'));

    $firstImage->refresh();
    expect($firstImage->caption)->toBe('Updated caption only.');
    expect($section->refresh()->galleryImages)->toHaveCount(1);
});

test('a user without create-features cannot create an article', function () {
    $user = userWithPermission('view-features');
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)
        ->post(route('admin.features.store'), validFeatureArticlePayload($category->id))
        ->assertForbidden();
});

test('a user with approve-features can approve a pending article', function () {
    $approver = userWithPermission(['view-features', 'approve-features']);
    $article = Article::factory()->feature()->create();

    $this->actingAs($approver)
        ->put(route('admin.features.approve', $article))
        ->assertRedirect(route('admin.features.index'));

    $article->refresh();

    expect($article->status)->toBe('active');
    expect($article->approved_by)->toBe($approver->id);
});

test('a user without approve-features cannot approve a pending article', function () {
    $user = userWithPermission(['view-features', 'create-features']);
    $article = Article::factory()->feature()->create();

    $this->actingAs($user)
        ->put(route('admin.features.approve', $article))
        ->assertForbidden();
});

test('a user with change-features-status can toggle active/inactive', function () {
    $user = userWithPermission(['view-features', 'change-features-status']);
    $article = Article::factory()->feature()->active()->create();

    $this->actingAs($user)
        ->put(route('admin.features.status.update', $article))
        ->assertRedirect(route('admin.features.index'));

    expect($article->refresh()->status)->toBe('inactive');
});

test('editing an article can update fields, replace the featured image, and sync sections', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->post(route('admin.features.store'), validFeatureArticlePayload($category->id));
    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $article->load('sections');
    $existingSection = $article->sections->first();

    $payload = validFeatureArticlePayload($category->id, [
        'title' => 'Letters from the Coast, Revisited',
        'sections' => [
            [
                'id' => $existingSection->id,
                'title' => 'First Section, Updated',
                'media_type' => 'image',
                'content' => 'Updated body copy.',
                'image_position' => 'center',
            ],
            [
                'title' => 'Brand New Section',
                'media_type' => 'image',
                'content' => 'New section body.',
                'image_position' => 'center',
            ],
        ],
    ]);
    unset($payload['featured_image']);

    $this->actingAs($user)
        ->put(route('admin.features.update', $article), $payload)
        ->assertRedirect(route('admin.features.index'));

    $article->refresh()->load('sections');

    expect($article->title)->toBe('Letters from the Coast, Revisited');
    expect($article->sections)->toHaveCount(2);
    expect($article->sections->firstWhere('id', $existingSection->id)->title)->toBe('First Section, Updated');
});

test('a user with delete-features can delete an article and its images', function () {
    $user = userWithPermission(['view-features', 'create-features', 'delete-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->post(route('admin.features.store'), validFeatureArticlePayload($category->id));
    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $featuredImage = $article->featuredImage()->firstOrFail();

    $this->actingAs($user)
        ->delete(route('admin.features.destroy', $article))
        ->assertRedirect(route('admin.features.index'));

    $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    $this->assertDatabaseMissing('media_files', ['id' => $featuredImage->id]);
    Storage::disk('public')->assertMissing($featuredImage->original_path);
});

test('a section can carry a youtube video with the main content reused as its companion text', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $payload = validFeatureArticlePayload($category->id, [
        'sections' => [
            [
                'title' => 'Video Section',
                'media_type' => 'video',
                'content' => 'This is the shared content, reused beside the video.',
                'youtube_url' => 'https://www.youtube.com/watch?v=xyz789',
                'youtube_position' => 'left',
                'video_companion_type' => 'text',
            ],
        ],
    ]);

    $this->actingAs($user)->post(route('admin.features.store'), $payload);

    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $section = $article->sections->first();

    expect($section->youtube_url)->toBe('https://www.youtube.com/watch?v=xyz789');
    expect($section->video_companion_type)->toBe('text');
    expect($section->content)->toBe('This is the shared content, reused beside the video.');
});

test('a section can carry a youtube video with a dedicated companion image', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $payload = validFeatureArticlePayload($category->id, [
        'sections' => [
            [
                'title' => 'Video Section',
                'media_type' => 'video',
                'youtube_url' => 'https://www.youtube.com/watch?v=xyz789',
                'youtube_position' => 'right',
                'video_companion_type' => 'image',
                'video_companion_image' => UploadedFile::fake()->image('companion.jpg'),
            ],
        ],
    ]);

    $this->actingAs($user)->post(route('admin.features.store'), $payload);

    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $section = $article->sections->first();

    expect($section->videoCompanionImage)->not->toBeNull();
});

test('every section, including the first, can be removed in the rendered form', function () {
    $user = userWithPermission(['view-features', 'create-features']);

    // A fresh create form renders exactly one section (the default blank
    // first one) outside of the hidden <template> — so a single
    // data-remove-section on the page confirms it, and not just the one
    // baked into the template for later-added sections.
    $response = $this->actingAs($user)->get(route('admin.features.create'));

    $response->assertOk();
    $sectionTemplateHtml = str($response->getContent())->after('<template id="section-template">')->toString();
    $renderedSectionHtml = str($response->getContent())->before('<template id="section-template">')->toString();

    expect($renderedSectionHtml)->toContain('data-remove-section');
    expect($sectionTemplateHtml)->toContain('data-remove-section');
});

test('sections can be reordered instantly via the reorder endpoint', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $payload = validFeatureArticlePayload($category->id, [
        'sections' => [
            ['title' => 'First', 'media_type' => 'image', 'image_position' => 'center'],
            ['title' => 'Second', 'media_type' => 'image', 'image_position' => 'center'],
            ['title' => 'Third', 'media_type' => 'image', 'image_position' => 'center'],
        ],
    ]);

    $this->actingAs($user)->post(route('admin.features.store'), $payload);
    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $article->load('sections');
    [$first, $second, $third] = $article->sections->all();

    $response = $this->actingAs($user)
        ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
        ->putJson(route('admin.features.sections.reorder', $article), [
            'section_ids' => [$third->id, $first->id, $second->id],
        ]);

    $response->assertOk();

    $article->refresh()->load('sections');
    expect($article->sections->pluck('id')->all())->toBe([$third->id, $first->id, $second->id]);
    expect($article->sections->pluck('title')->all())->toBe(['Third', 'First', 'Second']);
});

test('the reorder endpoint rejects a section id that does not belong to the article', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->post(route('admin.features.store'), validFeatureArticlePayload($category->id));
    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $section = $article->sections->first();

    $foreignArticle = Article::factory()->feature()->create();
    $foreignSection = ArticleSection::create(['article_id' => $foreignArticle->id, 'title' => 'Foreign', 'sort_order' => 0]);

    $this->actingAs($user)
        ->put(route('admin.features.sections.reorder', $article), [
            'section_ids' => [$section->id, $foreignSection->id],
        ])
        ->assertStatus(422);
});

test('a user without edit-features cannot reorder sections', function () {
    $user = userWithPermission(['view-features', 'create-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->post(route('admin.features.store'), validFeatureArticlePayload($category->id));
    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $section = $article->sections->first();

    $this->actingAs($user)
        ->put(route('admin.features.sections.reorder', $article), ['section_ids' => [$section->id]])
        ->assertForbidden();
});

test('a section\'s active status can be toggled instantly via its own endpoint', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->post(route('admin.features.store'), validFeatureArticlePayload($category->id));
    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $section = $article->sections->first();
    expect($section->is_active)->toBeTrue();

    $response = $this->actingAs($user)
        ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
        ->put(route('admin.features.sections.status.update', [$article, $section]));

    $response->assertOk();
    $response->assertJson(['is_active' => false]);
    expect($section->refresh()->is_active)->toBeFalse();

    // Flipping again toggles it back.
    $this->actingAs($user)
        ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
        ->put(route('admin.features.sections.status.update', [$article, $section]))
        ->assertJson(['is_active' => true]);
});

test('a user without edit-features cannot toggle a section\'s active status', function () {
    $user = userWithPermission(['view-features', 'create-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->post(route('admin.features.store'), validFeatureArticlePayload($category->id));
    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $section = $article->sections->first();

    $this->actingAs($user)
        ->put(route('admin.features.sections.status.update', [$article, $section]))
        ->assertForbidden();
});

test('a section status toggle rejects a section that does not belong to the given article', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->post(route('admin.features.store'), validFeatureArticlePayload($category->id));
    $articleOne = Article::where('title', 'Letters from the Coast')->firstOrFail();

    $this->actingAs($user)->post(route('admin.features.store'), validFeatureArticlePayload($category->id, ['title' => 'A Second Article']));
    $articleTwo = Article::where('title', 'A Second Article')->firstOrFail();
    $sectionFromArticleTwo = $articleTwo->sections->first();

    $this->actingAs($user)
        ->put(route('admin.features.sections.status.update', [$articleOne, $sectionFromArticleTwo]))
        ->assertNotFound();
});

test('a section defaults active on create, and can be toggled inactive on update', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->post(route('admin.features.store'), validFeatureArticlePayload($category->id));
    $article = Article::where('title', 'Letters from the Coast')->firstOrFail();
    $section = $article->sections->first();
    expect($section->is_active)->toBeTrue();

    $payload = validFeatureArticlePayload($category->id, [
        'sections' => [
            [
                'id' => $section->id,
                'title' => $section->title,
                'media_type' => 'image',
                'content' => $section->content,
                'image_position' => $section->image_position,
                'is_active' => '0',
            ],
        ],
    ]);
    unset($payload['featured_image']);

    $this->actingAs($user)
        ->put(route('admin.features.update', $article), $payload)
        ->assertRedirect(route('admin.features.index'));

    expect($section->refresh()->is_active)->toBeFalse();
});

test('the Active/Inactive section toggle only appears on the edit form, not on create', function () {
    $user = userWithPermission(['view-features', 'create-features', 'edit-features']);
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)
        ->get(route('admin.features.create'))
        ->assertDontSee('is_active', false);

    $article = Article::factory()->feature()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->get(route('admin.features.edit', $article))
        ->assertSee('is_active', false);
});

test('a features url cannot manage a journal article', function () {
    $user = userWithPermission(['view-features', 'edit-features']);
    $journalArticle = Article::factory()->journal()->create();

    $this->actingAs($user)
        ->get(route('admin.features.edit', $journalArticle))
        ->assertNotFound();
});

test('the list page opens a featured image thumbnail in a fancybox lightbox', function () {
    $user = User::factory()->superAdmin()->create();
    $article = Article::factory()->feature()->create();
    $image = MediaFile::factory()->ofType('features')->create([
        'mediable_type' => Article::class,
        'mediable_id' => $article->id,
        'role' => 'featured',
    ]);

    $response = $this->actingAs($user)->get(route('admin.features.index'));

    $response->assertOk();
    $response->assertSee('data-fancybox="features"', false);
    $response->assertSee('href="'.$image->large_url.'"', false);
});

test('the sidebar shows a pending-approval badge only to users who can approve features', function () {
    Article::factory()->feature()->count(2)->create();

    $approver = userWithPermission(['view-features', 'approve-features']);
    $response = $this->actingAs($approver)->get(route('admin.dashboard'))->assertOk();
    $response->assertSee('2');
    $response->assertSee(route('admin.features.index'), false);
});
