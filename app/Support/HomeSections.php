<?php

namespace App\Support;

use App\Models\Article;
use App\Models\HomeSectionArticle;

/**
 * The super admin's hand-picked articles for each homepage carousel/slider
 * section (Latest Pieces, The Selection, Features, Journal) — see
 * resources/views/frontend/partials/home/. Nothing here is curated
 * automatically; a section shows only what's been explicitly picked in
 * Admin > Home Page, and only while that pick is still an active article.
 */
class HomeSections
{
    /**
     * @var array<string, string>
     */
    public const LABELS = [
        HomeSectionArticle::SECTION_LATEST_PIECES => 'Latest Pieces',
        HomeSectionArticle::SECTION_THE_SELECTION => 'The Selection',
        HomeSectionArticle::SECTION_FEATURES => 'Features',
        HomeSectionArticle::SECTION_JOURNAL => 'Journal',
    ];

    /**
     * The Features page's own two sections (Highlighted / More from this
     * edition) — toggled from the Features list/edit admin pages, not the
     * Home Page Sections screen, and never offered for journal articles.
     *
     * @var array<string, string>
     */
    public const FEATURES_PAGE_LABELS = [
        HomeSectionArticle::SECTION_FEATURES_HIGHLIGHTED => 'Highlighted (Features page)',
        HomeSectionArticle::SECTION_FEATURES_EDITION => 'More from this edition (Features page)',
    ];

    /**
     * The chosen articles for a homepage section, in the flat card shape the
     * frontend partials already expect — an empty list (so the section can
     * hide itself entirely) when nothing has been picked, or a pick's
     * article has since been deactivated.
     *
     * @return list<array<string, mixed>>
     */
    public static function picks(string $section): array
    {
        return HomeSectionArticle::query()
            ->forSection($section)
            ->with('article.category', 'article.featuredImage')
            ->get()
            ->pluck('article')
            ->filter(fn (?Article $article): bool => $article !== null && $article->status === Article::STATUS_ACTIVE)
            ->map(fn (Article $article): array => ArticleCard::build($article, self::routeFor($article)))
            ->values()
            ->all();
    }

    public static function routeFor(Article $article): string
    {
        return $article->type === Article::TYPE_JOURNAL ? 'journal.article' : 'features.article';
    }

    /**
     * The sections a given article type can actually be toggled into on its
     * own list/edit page — Latest Pieces and The Selection are open to
     * either type, but the Features/Journal carousels are exclusive to
     * their own type (a journal entry has no business in the Features
     * homepage carousel, and vice versa).
     *
     * @return array<string, string>
     */
    public static function labelsFor(string $articleType): array
    {
        $excluded = match ($articleType) {
            Article::TYPE_FEATURE => HomeSectionArticle::SECTION_JOURNAL,
            Article::TYPE_JOURNAL => HomeSectionArticle::SECTION_FEATURES,
            default => null,
        };

        return collect(self::LABELS)->except($excluded)->all();
    }

    /**
     * Every section a given article type can be toggled into from its own
     * list/edit page — the homepage sections (via labelsFor()) plus, for
     * feature articles only, the Features page's own two sections.
     *
     * @return array<string, string>
     */
    public static function toggleableLabelsFor(string $articleType): array
    {
        $labels = self::labelsFor($articleType);

        if ($articleType === Article::TYPE_FEATURE) {
            $labels = [...$labels, ...self::FEATURES_PAGE_LABELS];
        }

        return $labels;
    }
}
