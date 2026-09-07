<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\HomeSectionArticle;
use App\Models\User;

beforeEach(function () {
    seedRbac();
});

test('a super admin can add an active article to a home section from its edit page', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $response = $this->actingAs($user)
        ->putJson(route('admin.features.home-sections.toggle', [$article, HomeSectionArticle::SECTION_FEATURES]));

    $response->assertOk()->assertJson(['active' => true]);
    $this->assertDatabaseHas('home_section_articles', [
        'section' => HomeSectionArticle::SECTION_FEATURES,
        'article_id' => $article->id,
    ]);
});

test('toggling a second time removes the article from the section', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_FEATURES, 'article_id' => $article->id, 'sort_order' => 0]);

    $response = $this->actingAs($user)
        ->putJson(route('admin.features.home-sections.toggle', [$article, HomeSectionArticle::SECTION_FEATURES]));

    $response->assertOk()->assertJson(['active' => false]);
    $this->assertDatabaseMissing('home_section_articles', [
        'section' => HomeSectionArticle::SECTION_FEATURES,
        'article_id' => $article->id,
    ]);
});

test('a pending (not yet active) article cannot be added to a home section', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->putJson(route('admin.features.home-sections.toggle', [$article, HomeSectionArticle::SECTION_FEATURES]))
        ->assertStatus(422);

    $this->assertDatabaseMissing('home_section_articles', ['article_id' => $article->id]);
});

test('a user without manage-home-sections cannot toggle', function () {
    $user = userWithPermission('edit-features');
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->putJson(route('admin.features.home-sections.toggle', [$article, HomeSectionArticle::SECTION_FEATURES]))
        ->assertForbidden();
});

test('a journal article cannot be toggled through the features route', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    $article = Article::factory()->journal()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->putJson(route('admin.features.home-sections.toggle', [$article, HomeSectionArticle::SECTION_JOURNAL]))
        ->assertNotFound();
});

test('an unknown section name 404s when toggling', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->putJson(route('admin.features.home-sections.toggle', [$article, 'not-a-real-section']))
        ->assertNotFound();
});

test('a journal article cannot be toggled into the Features home section', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    $article = Article::factory()->journal()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->putJson(route('admin.journals.home-sections.toggle', [$article, HomeSectionArticle::SECTION_FEATURES]))
        ->assertNotFound();

    $this->assertDatabaseMissing('home_section_articles', ['article_id' => $article->id]);
});

test('a feature article cannot be toggled into the Journal home section', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->putJson(route('admin.features.home-sections.toggle', [$article, HomeSectionArticle::SECTION_JOURNAL]))
        ->assertNotFound();

    $this->assertDatabaseMissing('home_section_articles', ['article_id' => $article->id]);
});

test('a feature article can still be toggled into Latest Pieces and The Selection', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->putJson(route('admin.features.home-sections.toggle', [$article, HomeSectionArticle::SECTION_LATEST_PIECES]))
        ->assertOk()->assertJson(['active' => true]);

    $this->actingAs($user)
        ->putJson(route('admin.features.home-sections.toggle', [$article, HomeSectionArticle::SECTION_THE_SELECTION]))
        ->assertOk()->assertJson(['active' => true]);
});

test('the Features list and edit page never offer the Journal toggle', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->get(route('admin.features.index'))
        ->assertSee('Features', false)
        ->assertDontSee('for="home-section-'.$article->id.'-journal"', false);

    $this->actingAs($user)
        ->get(route('admin.features.edit', $article))
        ->assertDontSee('for="home-section-journal"', false);
});

test('the Journals list and edit page never offer the Features toggle', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    $article = Article::factory()->journal()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->get(route('admin.journals.index'))
        ->assertDontSee('for="home-section-'.$article->id.'-features"', false);

    $this->actingAs($user)
        ->get(route('admin.journals.edit', $article))
        ->assertDontSee('for="home-section-features"', false);
});

test('a feature article can be toggled into the Features page\'s own Highlighted and Edition sections', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->putJson(route('admin.features.home-sections.toggle', [$article, HomeSectionArticle::SECTION_FEATURES_HIGHLIGHTED]))
        ->assertOk()->assertJson(['active' => true]);

    $this->actingAs($user)
        ->putJson(route('admin.features.home-sections.toggle', [$article, HomeSectionArticle::SECTION_FEATURES_EDITION]))
        ->assertOk()->assertJson(['active' => true]);

    $this->assertDatabaseHas('home_section_articles', ['section' => HomeSectionArticle::SECTION_FEATURES_HIGHLIGHTED, 'article_id' => $article->id]);
    $this->assertDatabaseHas('home_section_articles', ['section' => HomeSectionArticle::SECTION_FEATURES_EDITION, 'article_id' => $article->id]);
});

test('a journal article cannot be toggled into the Features page\'s Highlighted or Edition sections', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    $article = Article::factory()->journal()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->putJson(route('admin.journals.home-sections.toggle', [$article, HomeSectionArticle::SECTION_FEATURES_HIGHLIGHTED]))
        ->assertNotFound();

    $this->actingAs($user)
        ->putJson(route('admin.journals.home-sections.toggle', [$article, HomeSectionArticle::SECTION_FEATURES_EDITION]))
        ->assertNotFound();
});

test('the Features list and edit page offer the Highlighted and Edition toggles', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->get(route('admin.features.index'))
        ->assertSee('for="home-section-'.$article->id.'-features_highlighted"', false)
        ->assertSee('for="home-section-'.$article->id.'-features_edition"', false);

    $this->actingAs($user)
        ->get(route('admin.features.edit', $article))
        ->assertSee('for="home-section-features_highlighted"', false)
        ->assertSee('for="home-section-features_edition"', false);
});

test('the Journals list and edit page never offer the Highlighted or Edition toggles', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    $article = Article::factory()->journal()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->get(route('admin.journals.index'))
        ->assertDontSee('for="home-section-'.$article->id.'-features_highlighted"', false)
        ->assertDontSee('for="home-section-'.$article->id.'-features_edition"', false);

    $this->actingAs($user)
        ->get(route('admin.journals.edit', $article))
        ->assertDontSee('for="home-section-features_highlighted"', false)
        ->assertDontSee('for="home-section-features_edition"', false);
});

test('the Features list page splits toggles into a Home Page column and a Features Page column', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->get(route('admin.features.index'))
        ->assertSeeInOrder(['Home Page</th>', 'Features Page</th>'], false);
});

test('the Journals list page only shows a Home Page column, no Features Page column', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    Article::factory()->journal()->active()->create(['category_id' => $category->id]);

    $response = $this->actingAs($user)->get(route('admin.journals.index'));

    $response->assertSee('<th>Home Page</th>', false);
    $response->assertDontSee('<th>Features Page</th>', false);
});

test('the edit page\'s Page Sections tab groups toggles under Home Page and Features Page headings', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->get(route('admin.features.edit', $article))
        ->assertSee('Page Sections', false)
        ->assertSeeInOrder(['Home Page', 'Features Page'], false);
});

test('the journals edit page\'s Page Sections tab has no Features Page heading', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    $article = Article::factory()->journal()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->get(route('admin.journals.edit', $article))
        ->assertDontSee('Features Page', false);
});

test('the Home Page Sections screen never offers the Features page\'s own sections', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->get(route('admin.home-sections.edit'))
        ->assertDontSee('Highlighted (Features page)', false)
        ->assertDontSee('More from this edition (Features page)', false);
});

test('the edit page shows the Home Page Sections tab only for users who can manage-home-sections', function () {
    $canManage = User::factory()->superAdmin()->create();
    $cannotManage = userWithPermission('edit-features');
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($canManage)
        ->get(route('admin.features.edit', $article))
        ->assertSee('data-bs-target="#tab-home-sections"', false);

    $this->actingAs($cannotManage)
        ->get(route('admin.features.edit', $article))
        ->assertDontSee('data-bs-target="#tab-home-sections"', false);
});

test('the list page shows the Home Page Sections toggles only for users who can manage-home-sections', function () {
    $canManage = User::factory()->superAdmin()->create();
    $cannotManage = userWithPermission('view-features');
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($canManage)
        ->get(route('admin.features.index'))
        ->assertSee('data-home-section-toggle', false);

    $this->actingAs($cannotManage)
        ->get(route('admin.features.index'))
        ->assertDontSee('data-home-section-toggle', false);
});
