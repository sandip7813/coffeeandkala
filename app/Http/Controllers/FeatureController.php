<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\ArticleContentFactory;
use App\Support\ArticleIndex;
use App\Support\FeatureCatalog;
use Illuminate\Contracts\View\View;

class FeatureController extends Controller
{
    public function index(): View
    {
        $entries = collect(FeatureCatalog::edition())
            ->map(fn (array $entry): array => [
                ...$entry,
                'href' => route('features.show', $entry['category_id']),
            ])
            ->all();

        return view('frontend.features', [
            'entries' => $entries,
            'categories' => FeatureCatalog::all(),
        ]);
    }

    public function show(string $category): View
    {
        $this->abortIfCategoryInactive($category);

        $current = FeatureCatalog::find($category);

        abort_if($current === null, 404);

        return view('frontend.features-category', [
            'category' => $current,
            'categories' => FeatureCatalog::all(),
        ]);
    }

    public function showArticle(string $category, string $article): View
    {
        $this->abortIfCategoryInactive($category);

        $found = FeatureCatalog::findArticle($category, $article);

        abort_if($found === null, 404);

        ['category' => $current, 'article' => $currentArticle] = $found;

        return view('frontend.article-detail', [
            'category' => $current,
            'article' => $currentArticle,
            'content' => ArticleContentFactory::build($currentArticle, $current, 'features'),
            'subcategories' => ArticleIndex::subcategories(route('features.show', $current['id'])),
            'recent' => ArticleIndex::recent(6, $currentArticle['href']),
            'source' => 'features',
            'sourceLabel' => 'Features',
            'sourceIndexHref' => route('features'),
            'categoryHref' => route('features.show', $current['id']),
            'sidebarPosition' => ArticleIndex::sidebarPosition($current['id']),
            'neighbors' => ArticleIndex::neighbors($current['articles'], $currentArticle['slug']),
        ]);
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
