<?php

namespace App\Support;

use App\Models\Article;

/**
 * Cross-catalog helpers for the article detail page's sidebar: the combined
 * sub-category directory (Features + Journal category chrome, unaffected by
 * this build) and a "recently published" feed merged from real Articles of
 * both types.
 */
class ArticleIndex
{
    /**
     * The sidebar's "Explore the Sections" tree: two root nodes — Features
     * and Journal — each holding their own sub-categories as children.
     *
     * @return list<array{
     *     name: string,
     *     href: string,
     *     icon: string,
     *     is_current_branch: bool,
     *     children: list<array{name: string, href: string, is_current: bool}>
     * }>
     */
    public static function subcategories(?string $currentHref = null): array
    {
        $features = collect(FeatureCatalog::all())->map(fn (array $category): array => [
            'name' => $category['name'],
            'href' => route('features.show', $category['id']),
            'is_current' => route('features.show', $category['id']) === $currentHref,
        ])->values()->all();

        $journal = collect(JournalCatalog::categories())->map(fn (array $category): array => [
            'name' => $category['name'],
            'href' => route('journal.category', $category['id']),
            'is_current' => route('journal.category', $category['id']) === $currentHref,
        ])->values()->all();

        return [
            [
                'name' => 'Features',
                'href' => route('features'),
                'icon' => 'fa-compass',
                'is_current_branch' => collect($features)->contains('is_current', true),
                'children' => $features,
            ],
            [
                'name' => 'Journal',
                'href' => route('journal'),
                'icon' => 'fa-feather',
                'is_current_branch' => collect($journal)->contains('is_current', true),
                'children' => $journal,
            ],
        ];
    }

    /**
     * Which rail the "Explore the Sections" / "Recently Published" panels sit
     * on for a given category — most categories keep the sidebar on the
     * right, but a few are called out here to sit on the left instead, so
     * not every article page reads the same. Easy to reassign per category
     * as the design settles.
     */
    public static function sidebarPosition(string $categoryId): string
    {
        $leftRail = [
            'on-a-budget',
            'not-on-the-atlas',
            'worth-knowing',
            'chapters-over-coffee',
        ];

        return in_array($categoryId, $leftRail, true) ? 'left' : 'right';
    }

    /**
     * The most recently published real articles across both Features and
     * Journals, for the sidebar's "Recently published" panel.
     *
     * @return list<array{title: string, category_name: string, category_href: string, date: string, date_label: string, href: string, image: string}>
     */
    public static function recent(int $limit = 6, ?string $excludeHref = null): array
    {
        $features = Article::query()
            ->ofType(Article::TYPE_FEATURE)
            ->active()
            ->with('featuredImage', 'category')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn (Article $article): ?array => self::recentEntry($article, 'features.show', 'features.article'))
            ->filter();

        $journal = Article::query()
            ->ofType(Article::TYPE_JOURNAL)
            ->active()
            ->with('featuredImage', 'category')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn (Article $article): ?array => self::recentEntry($article, 'journal.category', 'journal.article'))
            ->filter();

        return $features->concat($journal)
            ->reject(fn (array $entry): bool => $excludeHref !== null && $entry['href'] === $excludeHref)
            ->sortByDesc('date')
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return array{title: string, category_name: string, category_href: string, date: string, date_label: string, href: string, image: ?string}|null
     */
    private static function recentEntry(Article $article, string $categoryRoute, string $articleRoute): ?array
    {
        $categorySlug = $article->category?->slug;

        if ($categorySlug === null) {
            return null;
        }

        return [
            'title' => $article->title,
            'category_name' => $article->category->title,
            'category_href' => route($categoryRoute, $categorySlug),
            'date' => $article->created_at->toDateString(),
            'date_label' => $article->created_at->format('j M Y'),
            'href' => route($articleRoute, ['category' => $categorySlug, 'article' => $article->slug]),
            'image' => $article->featuredImage?->large_url,
        ];
    }

    /**
     * The article immediately before/after the current one within its own
     * chapter/category, in that chapter's own display order — for the
     * "previous article" / "next article" footer nav. Either side is null
     * at the first/last article in the chapter.
     *
     * @param  list<array<string, mixed>>  $siblings  every article/entry in the same category, in display order
     * @return array{previous: ?array{title: string, href: string}, next: ?array{title: string, href: string}}
     */
    public static function neighbors(array $siblings, string $currentSlug): array
    {
        $index = collect($siblings)->search(fn (array $item): bool => $item['slug'] === $currentSlug);

        if ($index === false) {
            return ['previous' => null, 'next' => null];
        }

        $previous = $siblings[$index - 1] ?? null;
        $next = $siblings[$index + 1] ?? null;

        return [
            'previous' => $previous === null ? null : ['title' => $previous['title'], 'href' => $previous['href']],
            'next' => $next === null ? null : ['title' => $next['title'], 'href' => $next['href']],
        ];
    }
}
