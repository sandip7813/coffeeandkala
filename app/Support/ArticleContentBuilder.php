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
    // How far a section title is indented in the Table of Contents, by its
    // heading level — H1 and H2 both sit at the top level.
    private const TOC_DEPTHS = ['h1' => 0, 'h2' => 0, 'h3' => 1, 'h4' => 2];

    /**
     * @return array{
     *     intro: string,
     *     toc: list<array{id: string, label: string, depth: int}>,
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
            // The element each renders as — chosen per field on the admin
            // form ('p' = "Normal", or h1–h4); whitelisted, so safe to use
            // as a tag name in the view.
            'heading_tag' => ArticleSection::headingLevel($section->title_level, ArticleSection::DEFAULT_TITLE_LEVEL),
            'subheading' => $section->subtitle,
            'subheading_tag' => ArticleSection::headingLevel($section->subtitle_level, ArticleSection::DEFAULT_SUBTITLE_LEVEL),
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
     * @return list<array{id: string, label: string, depth: int}>
     */
    private static function buildToc(Article $article, array $sections): array
    {
        $toc = collect([
            ['id' => 'introduction', 'label' => 'Introduction', 'depth' => 0],
            ['id' => 'editors-note', 'label' => "Editor's Note", 'depth' => 0],
        ]);

        // A section with no title — or whose title is set to "Normal"
        // rather than a heading — has nothing to list: it still renders on
        // the page (with its own anchor id), just without a Table of
        // Contents entry. H3/H4 titles are indented beneath the H1/H2 ones.
        $toc = $toc->concat(collect($sections)
            ->filter(fn (array $section): bool => filled($section['heading']) && $section['heading_tag'] !== 'p')
            ->map(fn (array $section): array => [
                'id' => $section['id'],
                'label' => $section['heading'],
                'depth' => self::TOC_DEPTHS[$section['heading_tag']] ?? 0,
            ]));

        if ($article->faqs->isNotEmpty()) {
            $toc->push(['id' => 'faq', 'label' => 'Frequently Asked Questions', 'depth' => 0]);
        }

        return $toc->push(['id' => 'authors-note', 'label' => "Author's Note", 'depth' => 0])->all();
    }
}
