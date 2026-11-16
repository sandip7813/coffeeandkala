<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/*
 * The admin's <x-adminlte-input> receives a bound :value already escaped
 * once by Blade, then escapes it again — so every form passes its value
 * wrapped in HtmlString to get exactly one escape. These guard against a
 * field regressing to the double-escaped "&amp;amp;" form, which would also
 * corrupt the value on the next save.
 */

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

const TRICKY = 'Tom & "Jerry" O\'Brien <b>';
const TRICKY_ESCAPED = 'Tom &amp; &quot;Jerry&quot; O&#039;Brien &lt;b&gt;';

test('article title and SEO fields show special characters escaped exactly once', function () {
    $article = Article::factory()->journal()->create([
        'category_id' => Category::query()->ofType(Category::TYPE_JOURNAL)->firstOrFail()->id,
        'title' => TRICKY,
    ]);
    $article->meta()->firstOrCreate([])->update(['title' => TRICKY, 'keywords' => TRICKY]);

    $html = $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.journals.edit', $article))
        ->assertOk()
        ->getContent();

    expect(substr_count($html, 'value="'.TRICKY_ESCAPED.'"'))->toBe(3);
    expect($html)->not->toContain('&amp;amp;');
});

test('saving an article without touching its title keeps the title intact', function () {
    $admin = User::factory()->superAdmin()->create();
    $article = Article::factory()->journal()->create([
        'category_id' => Category::query()->ofType(Category::TYPE_JOURNAL)->firstOrFail()->id,
        'title' => TRICKY,
    ]);

    // What the browser would send back from the edit form, unchanged.
    $html = $this->actingAs($admin)->get(route('admin.journals.edit', $article))->getContent();
    preg_match('/name="title"\s+id="title"\s+value="([^"]*)"/', $html, $match);
    $submittedTitle = html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5);

    expect($submittedTitle)->toBe(TRICKY);
});

test('user, profile and role names show special characters escaped exactly once', function () {
    $admin = User::factory()->superAdmin()->create(['first_name' => TRICKY]);
    $other = User::factory()->create(['last_name' => TRICKY]);
    $role = Role::factory()->create(['label' => TRICKY]);

    foreach ([route('admin.profile.edit'), route('admin.users.edit', $other), route('admin.roles.edit', $role)] as $url) {
        $this->actingAs($admin)
            ->get($url)
            ->assertOk()
            ->assertSee('value="'.TRICKY_ESCAPED.'"', false)
            ->assertDontSee('&amp;amp;', false);
    }
});
