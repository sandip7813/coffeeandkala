<?php

namespace App\Actions;

use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;

/**
 * Creates/updates an article's dynamic content sections (and their attached
 * images) and its FAQ list from validated request data. Used by both
 * store() and update() on the article controllers.
 *
 * Sections/FAQs carrying an 'id' are updated in place (so a section's
 * attached images, which point at it via media_files.mediable_id, stay
 * attached); rows without an 'id' are new; existing rows whose 'id' is no
 * longer present are deleted, along with their attached images/files.
 */
class SyncArticleSections
{
    public function __construct(private readonly StoreMediaFile $storeMediaFile) {}

    /**
     * @param  list<array<string, mixed>>  $sectionsData
     * @param  list<array<string, mixed>>  $faqsData
     */
    public function handle(Article $article, array $sectionsData, array $faqsData, User $user): void
    {
        $this->syncSections($article, $sectionsData, $user);
        $this->syncFaqs($article, $faqsData);
    }

    /**
     * @param  list<array<string, mixed>>  $sectionsData
     */
    private function syncSections(Article $article, array $sectionsData, User $user): void
    {
        $keepIds = collect($sectionsData)->pluck('id')->filter()->all();

        $article->sections()
            ->whereNotIn('id', $keepIds ?: [0])
            ->get()
            ->each(fn (ArticleSection $section) => $this->deleteSection($section));

        foreach ($sectionsData as $index => $data) {
            $section = ! empty($data['id'])
                ? $article->sections()->whereKey($data['id'])->first()
                : null;

            $attributes = [
                'sort_order' => $index,
                'title' => $data['title'] ?? null,
                'media_type' => $data['media_type'] ?? null,
                'content' => $data['content'] ?? null,
                // The column is NOT NULL (default 'none') — 'none' is no
                // longer offered in the UI (media_type governs whether an
                // image applies at all) but stays the internal "no image"
                // sentinel so this never needs to insert an explicit null.
                'image_position' => $data['image_position'] ?? 'none',
                'content_position' => $data['content_position'] ?? null,
                'youtube_url' => $data['youtube_url'] ?? null,
                'youtube_position' => $data['youtube_position'] ?? null,
                'video_companion_type' => $data['video_companion_type'] ?? null,
                // The toggle only appears on the edit form (not on Add
                // Article), so its absence means "not shown" rather than
                // "turned off" — default active in that case.
                'is_active' => array_key_exists('is_active', $data) ? (bool) $data['is_active'] : true,
            ];

            $section = $section
                ? tap($section)->update($attributes)
                : $article->sections()->create($attributes);

            $this->syncSectionImage($section, $data, $user, $article->type);
            $this->syncSectionGallery($section, $data, $user, $article->type);
            $this->syncVideoCompanionImage($section, $data, $user, $article->type);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncSectionImage(ArticleSection $section, array $data, User $user, string $type): void
    {
        $this->syncSingleImage(
            $section, $type, $user, 'section-image',
            $section->image()->first(),
            $data['image'] ?? null,
            $data['image_caption'] ?? '',
            ! empty($data['remove_image']),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncVideoCompanionImage(ArticleSection $section, array $data, User $user, string $type): void
    {
        $this->syncSingleImage(
            $section, $type, $user, 'video-companion-image',
            $section->videoCompanionImage()->first(),
            $data['video_companion_image'] ?? null,
            $data['video_companion_image_caption'] ?? '',
            ! empty($data['remove_video_companion_image']),
        );
    }

    /**
     * Shared logic for a section's single-image slots (the positioned
     * section image, and the video's companion image): replaces the file
     * when a new upload is given, deletes it when removed, and otherwise
     * just keeps the caption in sync — without touching the file at all.
     */
    private function syncSingleImage(ArticleSection $section, string $type, User $user, string $role, ?MediaFile $existing, ?UploadedFile $image, string $caption, bool $remove): void
    {
        if ($image) {
            if ($existing) {
                $this->storeMediaFile->deleteFiles($existing);
                $existing->delete();
            }

            $this->storeMediaFile->handleForMediable(
                $type, $image, $user, ArticleSection::class, $section->id, $role, 0, $caption,
            );

            return;
        }

        if ($remove) {
            if ($existing) {
                $this->storeMediaFile->deleteFiles($existing);
                $existing->delete();
            }

            return;
        }

        if ($existing && $existing->caption !== $caption) {
            $existing->update(['caption' => $caption]);
        }
    }

    /**
     * Syncs a section's Gallery Images the same way sections/faqs sync:
     * each slot carries its own optional 'id' (update in place — keeping
     * the same MediaFile row lets a caption-only edit skip re-uploading),
     * 'image' (a new/replacement file), and 'caption'. A slot with neither
     * an id nor an image is an abandoned "Add Image" click and is silently
     * skipped rather than treated as an error.
     *
     * @param  array<string, mixed>  $data
     */
    private function syncSectionGallery(ArticleSection $section, array $data, User $user, string $type): void
    {
        /** @var list<array<string, mixed>> $slots */
        $slots = $data['gallery'] ?? [];

        $keepIds = collect($slots)->pluck('id')->filter()->all();

        $section->galleryImages()
            ->whereNotIn('id', $keepIds ?: [0])
            ->get()
            ->each(function (MediaFile $mediaFile): void {
                $this->storeMediaFile->deleteFiles($mediaFile);
                $mediaFile->delete();
            });

        foreach ($slots as $index => $slot) {
            /** @var UploadedFile|null $image */
            $image = $slot['image'] ?? null;
            $caption = $slot['caption'] ?? '';

            $existing = ! empty($slot['id'])
                ? $section->galleryImages()->whereKey($slot['id'])->first()
                : null;

            if ($existing) {
                if ($image) {
                    $this->storeMediaFile->deleteFiles($existing);
                    $existing->delete();
                    $this->storeMediaFile->handleForMediable(
                        $type, $image, $user, ArticleSection::class, $section->id, 'gallery', $index, $caption,
                    );
                } else {
                    $existing->update(['caption' => $caption, 'sort_order' => $index]);
                }

                continue;
            }

            if ($image) {
                $this->storeMediaFile->handleForMediable(
                    $type, $image, $user, ArticleSection::class, $section->id, 'gallery', $index, $caption,
                );
            }
        }
    }

    private function deleteSection(ArticleSection $section): void
    {
        $section->image()->get()->each(function (MediaFile $mediaFile): void {
            $this->storeMediaFile->deleteFiles($mediaFile);
            $mediaFile->delete();
        });

        $section->galleryImages()->get()->each(function (MediaFile $mediaFile): void {
            $this->storeMediaFile->deleteFiles($mediaFile);
            $mediaFile->delete();
        });

        $section->videoCompanionImage()->get()->each(function (MediaFile $mediaFile): void {
            $this->storeMediaFile->deleteFiles($mediaFile);
            $mediaFile->delete();
        });

        $section->delete();
    }

    /**
     * @param  list<array<string, mixed>>  $faqsData
     */
    private function syncFaqs(Article $article, array $faqsData): void
    {
        $keepIds = collect($faqsData)->pluck('id')->filter()->all();

        $article->faqs()->whereNotIn('id', $keepIds ?: [0])->delete();

        foreach ($faqsData as $index => $data) {
            $attributes = [
                'question' => $data['question'] ?? null,
                'answer' => $data['answer'] ?? null,
                'sort_order' => $index,
            ];

            if (! empty($data['id'])) {
                $article->faqs()->whereKey($data['id'])->update($attributes);
            } else {
                $article->faqs()->create($attributes);
            }
        }
    }
}
