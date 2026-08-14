<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdateProfilePhoto
{
    /**
     * Store the uploaded photo, generate a thumbnail, remove any previous
     * files, and persist the new paths on the user.
     */
    public function handle(User $user, UploadedFile $file): void
    {
        $disk = config('media.profile_photo.disk');
        $directory = config('media.profile_photo.directory');
        $thumbnailDirectory = config('media.profile_photo.thumbnail_directory');

        $filename = sprintf(
            '%d_%s.%s',
            $user->id,
            Str::random(12),
            $file->getClientOriginalExtension(),
        );

        $path = $file->storeAs($directory, $filename, $disk);
        $thumbnailPath = $this->storeThumbnail($file, $thumbnailDirectory, $filename, $disk);

        $this->deleteExisting($user, $disk);

        $user->forceFill([
            'profile_photo_path' => $path,
            'profile_photo_thumbnail_path' => $thumbnailPath,
        ])->save();
    }

    private function storeThumbnail(UploadedFile $file, string $thumbnailDirectory, string $filename, string $disk): ?string
    {
        $width = config('media.profile_photo.thumbnail.width');
        $height = config('media.profile_photo.thumbnail.height');
        $mimeType = $file->getMimeType();

        $source = $this->readImage($file->getRealPath(), $mimeType);

        if (! $source) {
            return null;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $cropSize = min($sourceWidth, $sourceHeight);
        $cropX = (int) (($sourceWidth - $cropSize) / 2);
        $cropY = (int) (($sourceHeight - $cropSize) / 2);

        $thumbnail = imagecreatetruecolor($width, $height);

        if ($mimeType === 'image/jpeg') {
            $white = imagecolorallocate($thumbnail, 255, 255, 255);
            imagefill($thumbnail, 0, 0, $white);
        } else {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
            imagefill($thumbnail, 0, 0, $transparent);
        }

        imagecopyresampled(
            $thumbnail, $source,
            0, 0, $cropX, $cropY,
            $width, $height, $cropSize, $cropSize,
        );

        $contents = $this->encodeImage($thumbnail, $mimeType);

        imagedestroy($source);
        imagedestroy($thumbnail);

        $thumbnailPath = $thumbnailDirectory.'/'.$filename;

        Storage::disk($disk)->put($thumbnailPath, $contents);

        return $thumbnailPath;
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

    private function deleteExisting(User $user, string $disk): void
    {
        foreach ([$user->profile_photo_path, $user->profile_photo_thumbnail_path] as $path) {
            if ($path && Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
    }
}
