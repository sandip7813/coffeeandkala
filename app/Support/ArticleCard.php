<?php

namespace App\Support;

use App\Models\Article;
use Illuminate\Support\Str;

/**
 * Maps a real, DB-backed Article onto the flat card shape the Features/
 * Journal frontend views already expect (title/excerpt/tag/date/date_label/
 * image/href/slug) — the same shape FeatureCatalog/JournalCatalog's
 * hand-authored article arrays used to provide, so the listing/edition
 * partials don't need to change, only where the data comes from.
 */
class ArticleCard
{
    /**
     * @return array{
     *     title: string,
     *     excerpt: string,
     *     tag: string,
     *     date: string,
     *     date_label: string,
     *     image: ?string,
     *     thumbnail: ?string,
     *     slug: string,
     *     href: string,
     *     category_id: ?string,
     *     category_name: ?string
     * }
     */
    public static function build(Article $article, string $articleRoute): array
    {
        $categorySlug = $article->category?->slug;

        return [
            'title' => $article->title,
            'excerpt' => Str::of($article->introduction)->stripTags()->squish()->toString(),
            // No distinct "tag" concept exists on Article (the old catalog's
            // tag was an editorial label separate from its category, e.g.
            // "Dispatch") — kept as the category title only so callers that
            // still reference 'tag' don't break; category_name is the one
            // to actually display, since showing both together duplicates
            // the same text.
            'tag' => $article->category?->title ?? '',
            'date' => $article->created_at->toDateString(),
            'date_label' => $article->created_at->format('j M Y'),
            'image' => $article->featuredImage?->large_url,
            'thumbnail' => $article->featuredImage?->thumbnail_url,
            'slug' => $article->slug,
            'href' => $categorySlug === null ? '#' : route($articleRoute, ['category' => $categorySlug, 'article' => $article->slug]),
            'category_id' => $categorySlug,
            'category_name' => $article->category?->title,
        ];
    }
}
