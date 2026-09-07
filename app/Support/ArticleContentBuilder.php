<?php

namespace App\Support;

use App\Models\Article;
use App\Models\ArticleSection;
use Illuminate\Support\Str;

/**
 * Builds the article detail page's body straight from the real, admin-
 * authored Article/ArticleSection/ArticleFaq rows — replaces the old
 * ArticleContentFactory, which fabricated this content from a title seed.
 * Every field here is exactly what the admin entered; nothing is derived
 * or invented.
 */
class ArticleContentBuilder
{
    /**
     * @return array{
     *     intro: string,
     *     toc: list<array{id: string, label: string}>,
     *     editors_note: array{body: string},
     *     sections: list<array<string, mixed>>,
     *     faq: list<array{question: string, answer: string}>,
     *     authors_note: array{body: string}
     * }
     */
    public static function build(Article $article): array
    {
        $sections = $article->sections
            ->where('is_active', true)
            ->values()
            ->map(fn (ArticleSection $section, int $index): array => self::buildSection($section, $index))
            ->all();

        return [
            'intro' => HtmlSanitizer::stripPresentationalMarkup($article->introduction),
            'toc' => self::buildToc($article, $sections),
            'editors_note' => ['body' => HtmlSanitizer::stripPresentationalMarkup($article->editors_note)],
            'sections' => $sections,
            'faq' => $article->faqs->map(fn ($faq): array => [
                'question' => $faq->question,
                'answer' => $faq->answer,
            ])->all(),
            // No page/category title here — just the author's own note.
            'authors_note' => [
                'body' => HtmlSanitizer::stripPresentationalMarkup($article->authors_note),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function buildSection(ArticleSection $section, int $index): array
    {
        return [
            'id' => self::sectionId($section, $index),
            'heading' => $section->title,
            'media_type' => $section->media_type,
            'image_position' => $section->image_position,
            'content_position' => $section->content_position,
            'content_html' => HtmlSanitizer::stripPresentationalMarkup($section->content),
            // Left/right sections show the smaller thumbnail (it sits
            // beside the text, so it never needs to be large); a
            // center/standalone section shows the full large copy.
            'image' => $section->image?->large_url,
            'image_thumb' => $section->image?->thumbnail_url,
            'gallery' => $section->galleryImages->map(fn ($media): array => [
                'image' => $media->large_url,
                'thumb' => $media->thumbnail_url,
                'caption' => $media->caption,
            ])->all(),
            'youtube_url' => $section->youtube_url,
            'youtube_position' => $section->youtube_position,
            'video_companion_type' => $section->video_companion_type,
            'video_companion_image' => $section->videoCompanionImage?->large_url,
        ];
    }

    private static function sectionId(ArticleSection $section, int $index): string
    {
        return filled($section->title) ? Str::slug($section->title) : "section-{$index}";
    }

    /**
     * @param  list<array<string, mixed>>  $sections
     * @return list<array{id: string, label: string}>
     */
    private static function buildToc(Article $article, array $sections): array
    {
        $toc = collect([
            ['id' => 'introduction', 'label' => 'Introduction'],
            ['id' => 'editors-note', 'label' => "Editor's Note"],
        ]);

        // A section with no title has nothing meaningful to list — it still
        // renders on the page (with its own generated anchor id), just
        // without a Table of Contents entry.
        $toc = $toc->concat(collect($sections)
            ->filter(fn (array $section): bool => filled($section['heading']))
            ->map(fn (array $section): array => ['id' => $section['id'], 'label' => $section['heading']]));

        if ($article->faqs->isNotEmpty()) {
            $toc->push(['id' => 'faq', 'label' => 'Frequently Asked Questions']);
        }

        return $toc->push(['id' => 'authors-note', 'label' => "Author's Note"])->all();
    }
}
