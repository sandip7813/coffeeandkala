<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\ArticleContentFactory;
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
            'categoryHighlights' => JournalCatalog::categoryHighlights(),
        ]);
    }

    public function show(Request $request, string $category): View
    {
        $this->abortIfCategoryInactive($category);

        $current = JournalCatalog::findCategory($category);

        abort_if($current === null, 404);

        $entries = JournalCatalog::forCategory($category);
        $perPage = 6;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $paginator = new LengthAwarePaginator(
            array_slice($entries, ($page - 1) * $perPage, $perPage),
            count($entries),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('frontend.journal-category', [
            'category' => $current,
            'categories' => JournalCatalog::categories(),
            'entries' => $paginator,
        ]);
    }

    public function showArticle(string $category, string $article): View
    {
        $this->abortIfCategoryInactive($category);

        $found = JournalCatalog::findEntry($category, $article);

        abort_if($found === null, 404);

        ['category' => $current, 'article' => $currentArticle] = $found;

        return view('frontend.article-detail', [
            'category' => $current,
            'article' => $currentArticle,
            'content' => ArticleContentFactory::build($currentArticle, $current, 'journal'),
            'subcategories' => ArticleIndex::subcategories(route('journal.category', $current['id'])),
            'recent' => ArticleIndex::recent(6, $currentArticle['href']),
            'source' => 'journal',
            'sourceLabel' => 'Journal',
            'sourceIndexHref' => route('journal'),
            'categoryHref' => route('journal.category', $current['id']),
            'sidebarPosition' => ArticleIndex::sidebarPosition($current['id']),
            'neighbors' => ArticleIndex::neighbors(JournalCatalog::forCategory($current['id']), $currentArticle['slug']),
        ]);
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
