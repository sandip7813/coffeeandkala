<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Meta;
use App\Support\ArticleCard;
use App\Support\ArticleContentBuilder;
use App\Support\ArticleIndex;
use App\Support\JournalCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class JournalController extends Controller
{
    public function index(): View
    {
        return view('frontend.journal', [
            'categoryHighlights' => $this->categoryHighlights(),
            'meta' => Meta::forPage('journal'),
        ]);
    }

    public function show(Request $request, string $category): View
    {
        $this->abortIfCategoryInactive($category);

        $current = JournalCatalog::findCategory($category);

        abort_if($current === null, 404);

        $entries = $this->entriesForCategory($category);
        $perPage = 6;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $paginator = new LengthAwarePaginator(
            array_slice($entries, ($page - 1) * $perPage, $perPage),
            count($entries),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categoryModel = Category::query()->ofType(Category::TYPE_JOURNAL)->where('slug', $category)->first();

        return view('frontend.journal-category', [
            'category' => $current,
            'categories' => JournalCatalog::categories(),
            'entries' => $paginator,
            'meta' => $categoryModel?->meta()->firstOrCreate([]),
        ]);
    }

    public function showArticle(string $category, string $article): View
    {
        $this->abortIfCategoryInactive($category);

        $current = JournalCatalog::findCategory($category);

        abort_if($current === null, 404);

        $categoryModel = Category::query()->ofType(Category::TYPE_JOURNAL)->where('slug', $category)->active()->first();

        $articleModel = $categoryModel === null ? null : Article::query()
            ->ofType(Article::TYPE_JOURNAL)
            ->active()
            ->where('category_id', $categoryModel->id)
            ->where('slug', $article)
            ->with('sections.image', 'sections.galleryImages', 'sections.videoCompanionImage', 'faqs', 'featuredImage', 'category')
            ->first();

        abort_if($articleModel === null, 404);

        $currentArticle = ArticleCard::build($articleModel, 'journal.article');

        return view('frontend.article-detail', [
            'category' => $current,
            'article' => $currentArticle,
            'content' => ArticleContentBuilder::build($articleModel),
            'subcategories' => ArticleIndex::subcategories(route('journal.category', $current['id'])),
            'recent' => ArticleIndex::recent(6, $currentArticle['href']),
            'source' => 'journal',
            'sourceLabel' => 'Journal',
            'sourceIndexHref' => route('journal'),
            'categoryHref' => route('journal.category', $current['id']),
            'sidebarPosition' => ArticleIndex::sidebarPosition($current['id']),
            'neighbors' => ArticleIndex::neighbors($this->entriesForCategory($current['id']), $currentArticle['slug']),
            'meta' => $articleModel->meta()->firstOrCreate([]),
        ]);
    }

    /**
     * Every real, active, admin-authored Article for a Journal category
     * (matched by slug), newest first, in the flat card shape the Journal
     * views expect.
     *
     * @return list<array<string, mixed>>
     */
    private function entriesForCategory(string $categorySlug): array
    {
        return Article::query()
            ->ofType(Article::TYPE_JOURNAL)
            ->active()
            ->whereHas('category', fn ($query) => $query->where('slug', $categorySlug))
            ->with('featuredImage', 'category')
            ->latest()
            ->get()
            ->map(fn (Article $article): array => ArticleCard::build($article, 'journal.article'))
            ->all();
    }

    /**
     * The newest article in each Journal category, for the index page's
     * "Explore by Category" highlights — same idea as JournalCatalog::
     * categoryHighlights() used to provide over static data.
     *
     * @return list<array<string, mixed>>
     */
    private function categoryHighlights(): array
    {
        return collect(JournalCatalog::categories())
            ->map(function (array $category): ?array {
                $entry = $this->entriesForCategory($category['id'])[0] ?? null;

                return $entry === null ? null : [...$entry, 'category_name' => $category['name']];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function abortIfCategoryInactive(string $category): void
    {
        $exists = Category::query()
            ->ofType(Category::TYPE_JOURNAL)
            ->where('slug', $category)
            ->active()
            ->exists();

        abort_unless($exists, 404);
    }
}
