<?php

use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

test('a super admin can view the Our Story edit page, which provisions the singleton article', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->get(route('admin.our-story.edit'))
        ->assertOk()
        ->assertSee('Our Story');

    $this->assertDatabaseHas('articles', ['type' => Article::TYPE_OUR_STORY, 'slug' => 'our-story']);
    $this->assertDatabaseHas('categories', ['type' => Category::TYPE_OUR_STORY, 'slug' => 'our-story']);
});

test('visiting the edit page twice reuses the same singleton article', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)->get(route('admin.our-story.edit'));
    $this->actingAs($user)->get(route('admin.our-story.edit'));

    expect(Article::query()->ofType(Article::TYPE_OUR_STORY)->count())->toBe(1);
    expect(Category::query()->ofType(Category::TYPE_OUR_STORY)->count())->toBe(1);
});

test('a user without manage-our-story cannot view the edit page', function () {
    $user = userWithPermission('edit-features');

    $this->actingAs($user)
        ->get(route('admin.our-story.edit'))
        ->assertForbidden();
});

test('the edit page never offers Essentials, FAQs, or Page Sections — content only', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)->get(route('admin.our-story.edit'));

    $response->assertOk();
    $response->assertSee('id="article-sections"', false);
    $response->assertSee('Add Section', false);
    $response->assertDontSee('tab-essentials', false);
    $response->assertDontSee('tab-faqs', false);
    $response->assertDontSee('tab-home-sections', false);
    $response->assertDontSee('name="category_id"', false);
    $response->assertDontSee('name="introduction"', false);
});

test('a super admin can save Our Story content sections', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user)->get(route('admin.our-story.edit'));
    $article = Article::query()->ofType(Article::TYPE_OUR_STORY)->firstOrFail();

    $response = $this->actingAs($user)
        ->putJson(route('admin.our-story.update'), [
            'sections' => [
                [
                    'title' => 'How It Began',
                    'media_type' => 'image',
                    'content' => 'The founder\'s own words.',
                    'image_position' => 'left',
                    'content_position' => 'beside',
                    'image' => UploadedFile::fake()->image('section.jpg', 800, 600),
                    'image_caption' => 'A caption.',
                ],
            ],
        ]);

    $response->assertRedirect(route('admin.our-story.edit'));
    $this->assertDatabaseHas('article_sections', [
        'article_id' => $article->id,
        'title' => 'How It Began',
    ]);
});

test('a user without manage-our-story cannot save Our Story content', function () {
    $user = userWithPermission('edit-features');

    $this->actingAs($user)
        ->putJson(route('admin.our-story.update'), ['sections' => []])
        ->assertForbidden();
});

test('a super admin can toggle a section\'s Active/Inactive flag instantly', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user)->get(route('admin.our-story.edit'));
    $article = Article::query()->ofType(Article::TYPE_OUR_STORY)->firstOrFail();
    $section = ArticleSection::create([
        'article_id' => $article->id,
        'sort_order' => 0,
        'title' => 'A Section',
        'media_type' => null,
        'content' => '<p>Body.</p>',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)
        ->putJson(route('admin.our-story.sections.status.update', [$article, $section]));

    $response->assertOk()->assertJson(['is_active' => false]);
    expect($section->fresh()->is_active)->toBeFalse();
});

test('a super admin can reorder sections instantly', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user)->get(route('admin.our-story.edit'));
    $article = Article::query()->ofType(Article::TYPE_OUR_STORY)->firstOrFail();
    $first = ArticleSection::create(['article_id' => $article->id, 'sort_order' => 0, 'title' => 'First', 'is_active' => true]);
    $second = ArticleSection::create(['article_id' => $article->id, 'sort_order' => 1, 'title' => 'Second', 'is_active' => true]);

    $response = $this->actingAs($user)
        ->putJson(route('admin.our-story.sections.reorder', $article), [
            'section_ids' => [$second->id, $first->id],
        ]);

    $response->assertOk();
    expect($second->fresh()->sort_order)->toBe(0);
    expect($first->fresh()->sort_order)->toBe(1);
});

test('the Our Story admin routes 404 when pointed at a non-Our-Story article', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);
    $section = ArticleSection::create(['article_id' => $article->id, 'sort_order' => 0, 'title' => 'X', 'is_active' => true]);

    $this->actingAs($user)
        ->putJson(route('admin.our-story.sections.status.update', [$article, $section]))
        ->assertNotFound();
});
