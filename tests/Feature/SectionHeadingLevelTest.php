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

function headingCategory(): Category
{
    return Category::query()->ofType(Category::TYPE_JOURNAL)->where('slug', 'worth-knowing')->firstOrFail();
}

function headingArticle(array $sections): Article
{
    $article = Article::factory()->journal()->active()->create(['category_id' => headingCategory()->id]);

    foreach ($sections as $index => $section) {
        $article->sections()->create([...$section, 'content' => '<p>Body.</p>', 'sort_order' => $index]);
    }

    return $article;
}

function headingPage(Article $article): string
{
    return test()->get(route('journal.article', ['category' => 'worth-knowing', 'article' => $article->slug]))
        ->assertOk()
        ->getContent();
}

test('section titles default to H2 and subtitles to H3', function () {
    $html = headingPage(headingArticle([['title' => 'Opening Topic', 'subtitle' => 'A closer look']]));

    expect($html)
        ->toContain('<h2 class="article-section-heading article-section-heading--h2">Opening Topic</h2>')
        ->toContain('<h3 class="article-section-subtitle article-section-subtitle--h3">A closer look</h3>');
});

test('each title and subtitle renders as its chosen level, including Normal', function () {
    $html = headingPage(headingArticle([
        ['title' => 'Big Opener', 'title_level' => 'h1', 'subtitle' => 'Plain line', 'subtitle_level' => 'p'],
        ['title' => 'Continuing', 'title_level' => 'h3', 'subtitle' => 'Minor note', 'subtitle_level' => 'h4'],
    ]));

    expect($html)
        ->toContain('<h1 class="article-section-heading article-section-heading--h1">Big Opener</h1>')
        ->toContain('<p class="article-section-subtitle article-section-subtitle--p">Plain line</p>')
        ->toContain('<h3 class="article-section-heading article-section-heading--h3">Continuing</h3>')
        ->toContain('<h4 class="article-section-subtitle article-section-subtitle--h4">Minor note</h4>');
});

test('the table of contents indents H3/H4 titles and skips Normal ones', function () {
    $html = headingPage(headingArticle([
        ['title' => 'Main Topic', 'title_level' => 'h2'],
        ['title' => 'Sub Topic', 'title_level' => 'h3'],
        ['title' => 'Minor Topic', 'title_level' => 'h4'],
        ['title' => 'Not A Heading', 'title_level' => 'p'],
    ]));

    $toc = str($html)->betweenFirst('<nav class="article-toc"', '</nav>')->toString();

    expect($toc)
        ->toMatch('/<li class="article-toc-item">\s*<a[^>]*data-scroll-target="main-topic"/')
        ->toMatch('/<li class="article-toc-item article-toc-item--depth-1">\s*<a[^>]*data-scroll-target="sub-topic"/')
        ->toMatch('/<li class="article-toc-item article-toc-item--depth-2">\s*<a[^>]*data-scroll-target="minor-topic"/')
        ->not->toContain('Not A Heading');

    // Still on the page itself, just not listed.
    expect($html)->toContain('>Not A Heading</p>');
});

test('heading levels are saved from the form, defaulting when not sent', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.journals.store'), [
            'category_id' => headingCategory()->id,
            'title' => 'Levels',
            'introduction' => '<p>Intro.</p>',
            'featured_image' => UploadedFile::fake()->image('featured.jpg', 1200, 900),
            'editors_note' => '<p>Editor.</p>',
            'authors_note' => '<p>Author.</p>',
            'sections' => [
                ['title' => 'Chosen', 'title_level' => 'h1', 'subtitle' => 'Sub', 'subtitle_level' => 'p'],
                ['title' => 'Defaults'],
            ],
        ])
        ->assertSessionHasNoErrors();

    $sections = Article::sole()->sections;
    expect($sections[0]->only('title_level', 'subtitle_level'))->toBe(['title_level' => 'h1', 'subtitle_level' => 'p']);
    expect($sections[1]->only('title_level', 'subtitle_level'))->toBe(['title_level' => 'h2', 'subtitle_level' => 'h3']);
});

test('an unknown heading level is rejected', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.journals.store'), [
            'category_id' => headingCategory()->id,
            'title' => 'Bad',
            'sections' => [['title' => 'X', 'title_level' => 'script']],
        ])
        ->assertSessionHasErrors('sections.0.title_level');
});

test('the section form offers Normal and H1–H4 beside both fields, preselecting the saved levels', function () {
    $article = headingArticle([['title' => 'Saved', 'title_level' => 'h4', 'subtitle' => 'Saved sub', 'subtitle_level' => 'h1']]);

    $html = $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.journals.edit', $article))
        ->assertOk()
        ->getContent();

    expect($html)
        ->toContain('name="sections[0][title_level]"')
        ->toContain('name="sections[0][subtitle_level]"')
        ->toMatch('/<option value="h4"\s+selected>H4<\/option>/')
        ->toMatch('/<option value="h1"\s+selected>H1<\/option>/');

    expect(array_keys(ArticleSection::HEADING_LEVELS))->toBe(['p', 'h1', 'h2', 'h3', 'h4']);
});

test('our story sections save heading levels too', function () {
    $admin = User::factory()->superAdmin()->create();
    $this->actingAs($admin)->get(route('admin.our-story.edit'));

    $this->actingAs($admin)
        ->put(route('admin.our-story.update'), [
            'sections' => [['title' => 'Our Beginning', 'title_level' => 'h1', 'subtitle' => 'Where it started', 'subtitle_level' => 'h4', 'content' => '<p>Story.</p>']],
        ])
        ->assertSessionHasNoErrors();

    $section = Article::query()->ofType(Article::TYPE_OUR_STORY)->sole()->sections()->sole();
    expect($section->only('title_level', 'subtitle', 'subtitle_level'))
        ->toBe(['title_level' => 'h1', 'subtitle' => 'Where it started', 'subtitle_level' => 'h4']);
});

test('previews use the unsaved heading levels', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.journals.preview.form'), [
            'category_id' => headingCategory()->id,
            'title' => 'Preview Levels',
            'sections' => [['title' => 'Previewed Heading', 'title_level' => 'h3']],
        ])
        ->assertOk()
        ->assertSee('<h3 class="article-section-heading article-section-heading--h3">Previewed Heading</h3>', false);
});
