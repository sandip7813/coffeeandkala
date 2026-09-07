<?php

namespace App\Support;

use App\Models\HomeSectionMedia;
use App\Models\MediaFile;

/**
 * The super admin's hand-picked images for the homepage's own Gallery and
 * Studio carousels — see resources/views/frontend/partials/home/
 * gallery-visual-storytelling.blade.php and visual-poetry.blade.php. Nothing
 * here is curated automatically; a section shows only what's been explicitly
 * toggled on from the Gallery/Studio admin list or edit page, and only while
 * that pick is still an active image.
 */
class HomeMediaSections
{
    /**
     * @var array<string, string>
     */
    public const LABELS = [
        HomeSectionMedia::SECTION_GALLERY => 'Gallery',
        HomeSectionMedia::SECTION_STUDIO => 'Studio',
    ];

    /**
     * The chosen images for a home media section, in a flat card shape —
     * an empty list (so the section can hide itself entirely) when nothing
     * has been picked, or a pick's image has since been deactivated.
     *
     * @return list<array{title: string, caption: string, image: ?string, thumb: ?string}>
     */
    public static function picks(string $section): array
    {
        return HomeSectionMedia::query()
            ->forSection($section)
            ->with('media')
            ->get()
            ->pluck('media')
            ->filter(fn (?MediaFile $media): bool => $media !== null && $media->status === MediaFile::STATUS_ACTIVE)
            ->map(fn (MediaFile $media): array => [
                'title' => $media->title,
                'caption' => $media->caption,
                'image' => $media->large_url,
                'thumb' => $media->thumbnail_url,
            ])
            ->values()
            ->all();
    }
}
