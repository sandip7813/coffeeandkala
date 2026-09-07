<?php

namespace App\Actions;

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreMediaFile
{
    /**
     * Store the uploaded image in its original, large, and thumbnail sizes,
     * and create the owning MediaFile record.
     */
    public function handle(string $type, UploadedFile $file, string $title, string $caption, User $uploadedBy): MediaFile
    {
        $paths = $this->storeSizes($type, $file);

        // Anyone who can approve this type's uploads (super admins included,
        // via the RBAC layer's built-in super-admin bypass) skips the pending
        // queue for their own uploads.
        $canApprove = $uploadedBy->can("approve-{$type}");

        return MediaFile::create([
            ...$paths,
            'type' => $type,
            'title' => $title,
            'caption' => $caption,
            'status' => $canApprove ? MediaFile::STATUS_ACTIVE : MediaFile::STATUS_PENDING,
            'uploaded_by' => $uploadedBy->id,
            'approved_by' => $canApprove ? $uploadedBy->id : null,
            'approved_at' => $canApprove ? now() : null,
        ]);
    }

    /**
     * Store an image attached to an Article/ArticleSection (featured image,
     * section image, or a gallery image within a section). Unlike gallery/
     * studio uploads these don't go through their own pending/approve cycle
     * — the owning Article's own approval covers them — so they're created
     * active straight away.
     */
    public function handleForMediable(string $type, UploadedFile $file, User $uploadedBy, string $mediableType, int $mediableId, string $role, int $sortOrder = 0, string $caption = ''): MediaFile
    {
        $paths = $this->storeSizes($type, $file);

        return MediaFile::create([
            ...$paths,
            'type' => $type,
            'title' => '',
            'caption' => $caption,
            'status' => MediaFile::STATUS_ACTIVE,
            'uploaded_by' => $uploadedBy->id,
            'mediable_type' => $mediableType,
            'mediable_id' => $mediableId,
            'role' => $role,
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * Store the original, large, and thumbnail copies of an upload for the
     * given media $type, returning the paths/filename to persist.
     *
     * @return array{file_name: string, thumbnail_path: string, large_path: string, original_path: string}
     */
    private function storeSizes(string $type, UploadedFile $file): array
    {
        $disk = config("media.{$type}.disk");
        $directory = config("media.{$type}.directory");
        $largeDirectory = config("media.{$type}.large_directory");
        $thumbnailDirectory = config("media.{$type}.thumbnail_directory");

        $filename = sprintf('%s.%s', Str::random(20), $file->getClientOriginalExtension());

        $originalPath = $file->storeAs($directory, $filename, $disk);
        $largePath = $this->storeResized(
            $file, $largeDirectory, $filename, $disk,
            config("media.{$type}.large.width"), config("media.{$type}.large.height"),
        );
        $thumbnailPath = $this->storeResized(
            $file, $thumbnailDirectory, $filename, $disk,
            config("media.{$type}.thumbnail.width"), config("media.{$type}.thumbnail.height"),
        );

        return [
            'file_name' => $filename,
            'thumbnail_path' => $thumbnailPath,
            'large_path' => $largePath,
            'original_path' => $originalPath,
        ];
    }

    /**
     * Delete a media file's stored original, large, and thumbnail images.
     */
    public function deleteFiles(MediaFile $mediaFile): void
    {
        $disk = config("media.{$mediaFile->type}.disk");

        foreach ([$mediaFile->original_path, $mediaFile->large_path, $mediaFile->thumbnail_path] as $path) {
            if ($path && Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
    }

    /**
     * Resize the uploaded image and store it — scaled down to fit within
     * the given bounds, preserving the original aspect ratio (never
     * cropped, never upscaled past the source's own size), for both the
     * large copy and the thumbnail alike.
     */
    private function storeResized(UploadedFile $file, string $directory, string $filename, string $disk, int $width, int $height): string
    {
        $mimeType = $file->getMimeType();
        $source = $this->readImage($file->getRealPath(), $mimeType);

        $path = $directory.'/'.$filename;

        if (! $source) {
            // Not a format we can process with GD — fall back to storing the
            // original bytes as-is rather than losing the upload.
            Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));

            return $path;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $scale = min($width / $sourceWidth, $height / $sourceHeight, 1);
        $destWidth = max(1, (int) round($sourceWidth * $scale));
        $destHeight = max(1, (int) round($sourceHeight * $scale));

        $destination = imagecreatetruecolor($destWidth, $destHeight);

        if ($mimeType === 'image/jpeg') {
            $white = imagecolorallocate($destination, 255, 255, 255);
            imagefill($destination, 0, 0, $white);
        } else {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 0, 0, 0, 127);
            imagefill($destination, 0, 0, $transparent);
        }

        imagecopyresampled(
            $destination, $source,
            0, 0, 0, 0,
            $destWidth, $destHeight, $sourceWidth, $sourceHeight,
        );

        $contents = $this->encodeImage($destination, $mimeType);

        imagedestroy($source);
        imagedestroy($destination);

        Storage::disk($disk)->put($path, $contents);

        return $path;
    }

    /**
     * @return \GdImage|false
     */
    private function readImage(string $path, ?string $mimeType)
    {
        return match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            default => false,
        };
    }

    private function encodeImage(\GdImage $image, ?string $mimeType): string
    {
        ob_start();

        match ($mimeType) {
            'image/jpeg' => imagejpeg($image, quality: 85),
            'image/webp' => imagewebp($image),
            default => imagepng($image),
        };

        return ob_get_clean();
    }
}
