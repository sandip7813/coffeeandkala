<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Poem;
use App\Models\User;

beforeEach(fn () => seedRbac());

test('guests cannot view the admin dashboard', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('authenticated staff can view the admin dashboard', function () {
    $user = User::factory()->editor()->create();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Dashboard', false);
    $response->assertSee('Coffee &amp; Kala site overview', false);
    $response->assertSee('Gallery plates', false);
    $response->assertSee('Studio works', false);
    $response->assertSee('Journal entries', false);
    $response->assertSee('Feature chapters', false);
    $response->assertSee('Recent journal', false);
    $response->assertSee('Recent features', false);
    $response->assertSee('Recent poetry', false);
});

test('a user only sees dashboard cards for permissions their role grants', function () {
    $user = userWithPermission('view-gallery');

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Gallery plates', false);
    $response->assertDontSee('Studio works', false);
    $response->assertDontSee('Journal entries', false);
    $response->assertDontSee('Feature chapters', false);
    $response->assertDontSee('Recent journal', false);
    $response->assertDontSee('Team members', false);
});

test('a user with no content permissions sees the empty-dashboard notice', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Team members', false);
    $response->assertSee('Roles', false);
    $response->assertDontSee('Gallery plates', false);
    $response->assertDontSee('Recent journal', false);
});

test('dashboard counts and recent lists reflect real, active database records — not the static catalog fixtures', function () {
    Poem::factory()->active()->create(['title' => 'The Only Published Poem']);
    Poem::factory()->pending()->create(['title' => 'A Pending Poem']);
    Article::factory()->journal()->active()->create(['title' => 'The Only Published Journal Entry']);
    Article::factory()->journal()->create(['title' => 'A Pending Journal Entry']);

    $user = User::factory()->editor()->create();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertSuccessful();

    // 1 active poem exists — the static PoetryCatalog fixture (6 poems)
    // must never leak into the count or the "Recent poetry" list.
    $response->assertSeeInOrder(['>1</span>', 'Poems'], false);
    $response->assertSee('The Only Published Poem', false);
    $response->assertDontSee('A Pending Poem', false);
    $response->assertDontSee('The Weight Of Rain', false);

    $response->assertSee('The Only Published Journal Entry', false);
    $response->assertDontSee('A Pending Journal Entry', false);
});

test('every dashboard link stays inside the admin panel, never the public site', function () {
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL, 'title' => 'Travel Diaries']);
    $article = Article::factory()->journal()->active()->create(['category_id' => $category->id, 'title' => 'A Slow Train Home']);
    $poem = Poem::factory()->active()->create(['title' => 'A Published Poem']);

    $user = userWithPermission(['view-journals', 'view-features', 'view-poetry', 'edit-journals', 'edit-poetry']);

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertSuccessful();

    // Stat cards point at their admin index, not the public route.
    $response->assertSee(route('admin.journals.index'), false);
    $response->assertDontSee('href="'.route('journal'), false);

    // A category breakdown row pre-filters the admin index by category_id.
    $response->assertSee(route('admin.journals.index', ['category_id' => $category->id]), false);

    // A "Recent journal" item with edit rights links straight to its admin
    // edit page, not the public article page.
    $response->assertSee(route('admin.journals.edit', $article), false);
    $response->assertDontSee('href="'.route('journal.article', ['category' => $category->slug, 'article' => $article->slug]), false);

    // Same for poetry.
    $response->assertSee(route('admin.poetry.edit', $poem), false);
    $response->assertDontSee('href="'.route('poetry.show', $poem->slug), false);
});

test('a viewer without edit rights gets sent to the admin index instead of an edit page they cannot open', function () {
    Article::factory()->journal()->active()->create(['title' => 'A Slow Train Home']);

    // view-journals only, no edit-journals.
    $user = userWithPermission('view-journals');

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee(route('admin.journals.index'), false);
});

test('super admins see every dashboard card and panel', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Gallery plates', false);
    $response->assertSee('Studio works', false);
    $response->assertSee('Journal entries', false);
    $response->assertSee('Feature chapters', false);
    $response->assertSee('Poems', false);
    $response->assertSee('Team members', false);
    $response->assertSee('Roles', false);
    $response->assertSee('Permissions', false);
    $response->assertSee('Recent journal', false);
    $response->assertSee('Recent features', false);
    $response->assertSee('Recent poetry', false);
});
