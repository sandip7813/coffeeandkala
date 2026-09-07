<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\HomeSectionArticle;
use App\Models\Meta;
use App\Support\ArticleCard;
use App\Support\ArticleContentBuilder;
use App\Support\ArticleIndex;
use App\Support\FeatureCatalog;
use App\Support\HomeSections;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class FeatureController extends Controller
{
    public function index(): View
    {
        return view('frontend.features', [
            'highlighted' => HomeSections::picks(HomeSectionArticle::SECTION_FEATURES_HIGHLIGHTED),
            'edition' => HomeSections::picks(HomeSectionArticle::SECTION_FEATURES_EDITION),
            'recentFeatures' => $this->recentFeatures(),
            'categories' => $this->categoriesWithArticles(),
            'meta' => Meta::forPage('features'),
        ]);
    }

    public function show(Request $request, string $category): View
    {
        $this->abortIfCategoryInactive($category);

        $current = collect($this->categoriesWithArticles())->firstWhere('id', $category);

        abort_if($current === null, 404);

        $articles = $current['articles'];
        $perPage = 6;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $entries = new LengthAwarePaginator(
            array_slice($articles, ($page - 1) * $perPage, $perPage),
            count($articles),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categoryModel = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', $category)->first();

        return view('frontend.features-category', [
            'category' => [...$current, 'articles' => $entries->items()],
            'categories' => $this->categoriesWithArticles(),
            'entries' => $entries,
            'meta' => $categoryModel?->meta()->firstOrCreate([]),
        ]);
    }

    public function showArticle(string $category, string $article): View
    {
        $this->abortIfCategoryInactive($category);

        $categoryModel = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', $category)->active()->first();

        $articleModel = $categoryModel === null ? null : Article::query()
            ->ofType(Article::TYPE_FEATURE)
            ->active()
            ->where('category_id', $categoryModel->id)
            ->where('slug', $article)
            ->with('sections.image', 'sections.galleryImages', 'sections.videoCompanionImage', 'faqs', 'featuredImage', 'category')
            ->first();

        abort_if($articleModel === null, 404);

        $current = collect($this->categoriesWithArticles())->firstWhere('id', $category);
        $currentArticle = ArticleCard::build($articleModel, 'features.article');

        return view('frontend.article-detail', [
            'category' => $current,
            'article' => $currentArticle,
            'content' => ArticleContentBuilder::build($articleModel),
            'subcategories' => ArticleIndex::subcategories(route('features.show', $current['id'])),
            'recent' => ArticleIndex::recent(6, $currentArticle['href']),
            'source' => 'features',
            'sourceLabel' => 'Features',
            'sourceIndexHref' => route('features'),
            'categoryHref' => route('features.show', $current['id']),
            'sidebarPosition' => ArticleIndex::sidebarPosition($current['id']),
            'neighbors' => ArticleIndex::neighbors($current['articles'], $currentArticle['slug']),
            'meta' => $articleModel->meta()->firstOrCreate([]),
        ]);
    }

    /**
     * Every Feature chapter's chrome (icon/lead/tagline/quote/cover/banner —
     * category management wasn't part of this build) with its 'articles'
     * replaced by the real, active, admin-authored Article rows for that
     * category — newest first.
     *
     * @return list<array<string, mixed>>
     */
    private function categoriesWithArticles(): array
    {
        return collect(FeatureCatalog::all())
            ->map(function (array $category): array {
                $articles = Article::query()
                    ->ofType(Article::TYPE_FEATURE)
                    ->active()
                    ->whereHas('category', fn ($query) => $query->where('slug', $category['id']))
                    ->with('featuredImage', 'category')
                    ->latest()
                    ->get()
                    ->map(fn (Article $article): array => ArticleCard::build($article, 'features.article'))
                    ->all();

                return [...$category, 'articles' => $articles];
            })
            ->all();
    }

    /**
     * The 12 most recently updated Feature articles — the "More from this
     * edition" section's own lower grid (below the admin-picked carousel),
     * always automatic rather than hand-picked.
     *
     * @return list<array<string, mixed>>
     */
    private function recentFeatures(): array
    {
        return Article::query()
            ->ofType(Article::TYPE_FEATURE)
            ->active()
            ->with('featuredImage', 'category')
            ->latest('updated_at')
            ->limit(12)
            ->get()
            ->map(fn (Article $article): array => ArticleCard::build($article, 'features.article'))
            ->all();
    }

    private function abortIfCategoryInactive(string $category): void
    {
        $exists = Category::query()
            ->ofType(Category::TYPE_FEATURE)
            ->where('slug', $category)
            ->active()
            ->exists();

        abort_unless($exists, 404);
    }
}
