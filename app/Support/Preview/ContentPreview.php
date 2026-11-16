<?php

namespace App\Support\Preview;

use App\Models\Article;
use App\Models\ArticleFaq;
use App\Models\ArticleSection;
use App\Models\Category;
use App\Models\MediaFile;
use App\Models\Meta;
use App\Models\Poem;
use Illuminate\Database\Eloquent\Collection;

/**
 * Builds the in-memory Article/Poem an admin preview renders — either the
 * saved record as-is (whatever its status), or what the edit form currently
 * holds, layered over the saved record. The form version mirrors how a real
 * save would interpret the same data (see SyncArticleSections), but nothing
 * is ever saved: the models are unsaved copies with their relations set by
 * hand, and newly picked images are temporary files (see PreviewUploads).
 */
class ContentPreview
{
    public static function savedArticle(Article $article): Article
    {
        return $article->load('sections.image', 'sections.galleryImages', 'sections.videoCompanionImage', 'faqs', 'featuredImage', 'category', 'meta');
    }

    /**
     * @param  array<string, mixed>  $data  the submitted form, files included
     */
    public static function articleFromForm(array $data, string $type, ?Article $base): Article
    {
        $base = $base ? self::savedArticle($base) : null;
        $article = $base ? clone $base : new Article(['type' => $type]);

        $categoryType = $type === Article::TYPE_FEATURE ? Category::TYPE_FEATURE : Category::TYPE_JOURNAL;
        $category = Category::query()->ofType($categoryType)->find($data['category_id'] ?? null)
            ?? $base?->category
            ?? Category::query()->ofType($categoryType)->active()->ordered()->first();

        abort_if($category === null, 404);

        $article->forceFill([
            'type' => $type,
            'category_id' => $category->id,
            'title' => self::text($data['title'] ?? null) ?? __('Untitled'),
            'subtitle' => self::text($data['subtitle'] ?? null),
            'introduction' => $data['introduction'] ?? '',
            'editors_note' => $data['editors_note'] ?? '',
            'authors_note' => $data['authors_note'] ?? '',
            'created_at' => $base?->created_at ?? now(),
            'updated_at' => now(),
        ]);
        $article->slug = self::text($data['slug'] ?? null) ?? $article->uniqueSlugFrom($article->title);

        $article->setRelation('category', $category);
        $article->setRelation('featuredImage', PreviewUploads::store($data['featured_image'] ?? null, $type) ?? $base?->featuredImage);
        $article->setRelation('sections', self::sectionsFromForm($data['sections'] ?? [], $type, $base));
        $article->setRelation('faqs', new Collection(
            collect($data['faqs'] ?? [])
                ->filter(fn ($faq): bool => is_array($faq) && (filled($faq['question'] ?? null) || filled($faq['answer'] ?? null)))
                ->values()
                ->map(fn (array $faq, int $index): ArticleFaq => (new ArticleFaq)->forceFill([
                    'question' => $faq['question'] ?? '',
                    'answer' => $faq['answer'] ?? '',
                    'sort_order' => $index,
                ]))
                ->all(),
        ));

        return $article;
    }

    public static function savedPoem(Poem $poem): Poem
    {
        return $poem->load('featuredImage', 'meta');
    }

    /**
     * @param  array<string, mixed>  $data  the submitted form, files included
     */
    public static function poemFromForm(array $data, ?Poem $base): Poem
    {
        $base = $base ? self::savedPoem($base) : null;
        $poem = $base ? clone $base : new Poem;

        $poem->forceFill([
            'title' => self::text($data['title'] ?? null) ?? __('Untitled'),
            'subtitle' => self::text($data['subtitle'] ?? null),
            'body' => (string) ($data['body'] ?? ''),
            'created_at' => $base?->created_at ?? now(),
            'updated_at' => now(),
        ]);
        $poem->slug = self::text($data['slug'] ?? null) ?? $poem->uniqueSlugFrom($poem->title);

        $poem->setRelation('featuredImage', PreviewUploads::store($data['featured_image'] ?? null, 'poetry') ?? $base?->featuredImage);

        return $poem;
    }

    /**
     * The SEO meta the form holds (the edit form's SEO tab), else the saved
     * record's own — never creating one.
     *
     * @param  array<string, mixed>  $data
     */
    public static function metaFromForm(array $data, Article|Poem|null $base): ?Meta
    {
        if (! array_key_exists('meta_title', $data) && ! array_key_exists('meta_description', $data) && ! array_key_exists('meta_keywords', $data)) {
            return $base?->meta;
        }

        return new Meta([
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ]);
    }

    /**
     * @param  mixed  $sectionsData
     * @return Collection<int, ArticleSection>
     */
    private static function sectionsFromForm($sectionsData, string $type, ?Article $base): Collection
    {
        $savedSections = $base?->sections->keyBy('id') ?? collect();

        $sections = collect(is_array($sectionsData) ? $sectionsData : [])
            ->filter(fn ($section): bool => is_array($section))
            ->values()
            ->map(function (array $data, int $index) use ($savedSections, $type): ArticleSection {
                /** @var ArticleSection|null $saved */
                $saved = ! empty($data['id']) ? $savedSections->get((int) $data['id']) : null;

                $section = (new ArticleSection)->forceFill([
                    'sort_order' => $index,
                    'title' => $data['title'] ?? null,
                    'title_level' => ArticleSection::headingLevel($data['title_level'] ?? null, ArticleSection::DEFAULT_TITLE_LEVEL),
                    'subtitle' => self::text($data['subtitle'] ?? null),
                    'subtitle_level' => ArticleSection::headingLevel($data['subtitle_level'] ?? null, ArticleSection::DEFAULT_SUBTITLE_LEVEL),
                    'media_type' => $data['media_type'] ?? null,
                    'content' => $data['content'] ?? null,
                    'image_position' => $data['image_position'] ?? 'none',
                    'content_position' => $data['content_position'] ?? null,
                    'youtube_url' => $data['youtube_url'] ?? null,
                    'youtube_position' => $data['youtube_position'] ?? null,
                    'video_companion_type' => $data['video_companion_type'] ?? null,
                    // Same rule as SyncArticleSections: the toggle only
                    // exists on the edit form, so its absence means active.
                    'is_active' => array_key_exists('is_active', $data) ? (bool) $data['is_active'] : true,
                ]);

                $section->setRelation('image', self::singleImage(
                    $data['image'] ?? null, $saved?->image, $data['image_caption'] ?? '', ! empty($data['remove_image']), $type,
                ));
                $section->setRelation('videoCompanionImage', self::singleImage(
                    $data['video_companion_image'] ?? null, $saved?->videoCompanionImage, $data['video_companion_image_caption'] ?? '', ! empty($data['remove_video_companion_image']), $type,
                ));
                $section->setRelation('galleryImages', self::gallery($data['gallery'] ?? [], $saved, $type));

                return $section;
            });

        return new Collection($sections->all());
    }

    private static function singleImage(mixed $upload, ?MediaFile $saved, ?string $caption, bool $remove, string $type): ?MediaFile
    {
        $new = PreviewUploads::store($upload, $type, $caption);

        if ($new !== null) {
            return $new;
        }

        return $remove ? null : self::withCaption($saved, $caption);
    }

    /**
     * @param  mixed  $slots
     * @return Collection<int, MediaFile>
     */
    private static function gallery($slots, ?ArticleSection $saved, string $type): Collection
    {
        $savedImages = $saved?->galleryImages->keyBy('id') ?? collect();

        $images = collect(is_array($slots) ? $slots : [])
            ->filter(fn ($slot): bool => is_array($slot))
            ->map(fn (array $slot): ?MediaFile => PreviewUploads::store($slot['image'] ?? null, $type, $slot['caption'] ?? '')
                ?? self::withCaption(! empty($slot['id']) ? $savedImages->get((int) $slot['id']) : null, $slot['caption'] ?? ''))
            ->filter()
            ->values();

        return new Collection($images->all());
    }

    private static function withCaption(?MediaFile $media, ?string $caption): ?MediaFile
    {
        return $media === null ? null : (clone $media)->forceFill(['caption' => $caption ?? '']);
    }

    private static function text(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
}
