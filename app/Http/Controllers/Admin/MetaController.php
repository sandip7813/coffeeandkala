<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Meta;
use App\Models\Poem;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * SEO meta data for the site's fixed, singleton static pages (see
 * Meta::STATIC_PAGES), plus a convenience listing — under the Features/
 * Journal/Poetry tabs — of every Feature/Journal category, Feature/Journal
 * article, and Poem, so their own meta can also be managed from here: the
 * same meta rows edited on each record's own admin edit screen (see
 * CategoryController/AbstractArticleController/PoemController), just
 * gathered in one place too.
 */
class MetaController extends Controller
{
    /**
     * Maps a content tab's {type} segment to the model it manages meta for.
     *
     * @var array<string, class-string>
     */
    private const CONTENT_TYPES = [
        'features' => Article::class,
        'journals' => Article::class,
        'poetry' => Poem::class,
        'feature-categories' => Category::class,
        'journal-categories' => Category::class,
    ];

    private const PER_PAGE = 10;

    public function edit(): View
    {
        abort_unless(auth()->user()?->can('manage-meta'), 403);

        // Every static page's own meta — including the Features/Journal/
        // Poetry index pages — lives together under the Main Pages tab;
        // those three tabs otherwise hold only their content listing.
        $mainMetas = collect(Meta::STATIC_PAGES)->mapWithKeys(
            fn (string $label, string $key) => [$key => Meta::forPage($key)]
        );

        return view('admin.meta.edit', [
            'mainMetas' => $mainMetas,
            'featureCategoriesListHtml' => $this->renderContentList('feature-categories', 1),
            'featuresListHtml' => $this->renderContentList('features', 1),
            'journalCategoriesListHtml' => $this->renderContentList('journal-categories', 1),
            'journalsListHtml' => $this->renderContentList('journals', 1),
            'poetryListHtml' => $this->renderContentList('poetry', 1),
        ]);
    }

    /**
     * Saves one static page's meta (a Main Pages tab entry — every static
     * page, including the Features/Journal/Poetry index pages, lives there).
     */
    public function updatePage(Request $request, string $key): RedirectResponse
    {
        abort_unless(auth()->user()?->can('manage-meta'), 403);
        abort_unless(array_key_exists($key, Meta::STATIC_PAGES), 404);

        $data = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ]);

        Meta::forPage($key)->update([
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ]);

        return redirect()->route('admin.meta.edit')->with('status', __('Meta data updated.'));
    }

    /**
     * One page of a content type's article/poem list, rendered server-side
     * and handed back as HTML for the tab to swap in via fetch — this is
     * what lets Features/Journal/Poetry page through more than 10 items
     * without a full page reload (see resources/js/admin-meta.js).
     */
    public function contentList(Request $request, string $type): JsonResponse
    {
        abort_unless(auth()->user()?->can('manage-meta'), 403);
        abort_unless(array_key_exists($type, self::CONTENT_TYPES), 404);

        $page = max((int) $request->query('page', 1), 1);

        return response()->json(['html' => $this->renderContentList($type, $page)]);
    }

    /**
     * Saves one Feature/Journal article's or Poem's meta from its row in
     * the matching tab — the same meta row editable on the record's own
     * edit screen.
     */
    public function updateContentMeta(Request $request, string $type, int $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('manage-meta'), 403);
        abort_unless(array_key_exists($type, self::CONTENT_TYPES), 404);

        $record = $this->contentQuery($type)->findOrFail($id);

        $data = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ]);

        $record->meta()->firstOrCreate([])->update([
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ]);

        return redirect()->route('admin.meta.edit')->with('status', __('Meta data updated.'));
    }

    private function contentQuery(string $type): Builder
    {
        return match ($type) {
            'features' => Article::query()->ofType(Article::TYPE_FEATURE),
            'journals' => Article::query()->ofType(Article::TYPE_JOURNAL),
            'poetry' => Poem::query(),
            'feature-categories' => Category::query()->ofType(Category::TYPE_FEATURE),
            'journal-categories' => Category::query()->ofType(Category::TYPE_JOURNAL),
        };
    }

    private function editRouteFor(string $type): string
    {
        return match ($type) {
            'features' => 'admin.features.edit',
            'journals' => 'admin.journals.edit',
            'poetry' => 'admin.poetry.edit',
            'feature-categories', 'journal-categories' => 'admin.categories.edit',
        };
    }

    private function renderContentList(string $type, int $page): string
    {
        $paginator = $this->contentQuery($type)->orderBy('title')->paginate(self::PER_PAGE, ['*'], 'page', $page);

        $items = collect($paginator->items())->map(fn ($record) => [
            'record' => $record,
            'meta' => $record->meta()->firstOrCreate([]),
        ]);

        return view('admin.meta._content-list', [
            'type' => $type,
            'items' => $items,
            'paginator' => $paginator,
            'editRoute' => $this->editRouteFor($type),
        ])->render();
    }
}
