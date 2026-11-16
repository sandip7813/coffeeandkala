<?php

namespace App\Support\Preview;

use App\Models\MediaFile;
use Illuminate\Http\UploadedFile;

/**
 * Images picked on an edit form but not saved yet, made viewable for a
 * preview: each is stored as-is under previews/ on the type's media disk
 * and wrapped in an unsaved MediaFile, so the frontend views can use its
 * large_url/thumbnail_url like any real image. Nothing is written to the
 * database; the files are deleted by `previews:prune` (scheduled hourly).
 */
class PreviewUploads
{
    public const DIRECTORY = 'previews';

    /**
     * An unsaved MediaFile for $file, or null if it isn't an acceptable
     * image for $type (wrong format, too large, failed upload) — same rules
     * the real save validates against (config/media.php).
     */
    public static function store(mixed $file, string $type, ?string $caption = null): ?MediaFile
    {
        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return null;
        }

        $formats = config("media.{$type}.formats", []);
        $maxSizeKb = config("media.{$type}.max_size_kb");

        if (
            ! in_array(strtolower((string) $file->guessExtension()), $formats, true)
            || ! str_starts_with((string) $file->getMimeType(), 'image/')
            || ($maxSizeKb && $file->getSize() > $maxSizeKb * 1024)
        ) {
            return null;
        }

        $path = $file->store(self::DIRECTORY, config("media.{$type}.disk"));

        if ($path === false) {
            return null;
        }

        return new MediaFile([
            'type' => $type,
            'thumbnail_path' => $path,
            'large_path' => $path,
            'caption' => $caption ?? '',
        ]);
    }

    /**
     * The media disks preview files can land on (one per previewable type).
     *
     * @return list<string>
     */
    public static function disks(): array
    {
        return collect(['features', 'journals', 'poetry'])
            ->map(fn (string $type): ?string => config("media.{$type}.disk"))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
