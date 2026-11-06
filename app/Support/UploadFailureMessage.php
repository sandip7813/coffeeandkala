<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

class UploadFailureMessage
{
    /**
     * Replace Laravel's generic "failed to upload" validation message with
     * the actual reason PHP rejected the file (most commonly: it is larger
     * than the server's upload limit, which PHP enforces before the request
     * ever reaches the `max:` validation rule). Falls back to the original
     * message when the reason is unknown.
     */
    public static function for(string $message, string $attribute, Validator $validator): string
    {
        $file = Arr::get($validator->getData(), $attribute);

        if (! $file instanceof UploadedFile) {
            return $message;
        }

        $name = $validator->getDisplayableAttribute($attribute);

        return match ($file->getError()) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => __('The :attribute is too large. The maximum allowed size is :size.', [
                'attribute' => $name,
                'size' => self::formatBytes((int) UploadedFile::getMaxFilesize()),
            ]),
            UPLOAD_ERR_PARTIAL => __('The :attribute was only partially uploaded. Please try again.', [
                'attribute' => $name,
            ]),
            default => $message,
        };
    }

    private static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), 1).' MB';
        }

        return round($bytes / 1024).' KB';
    }
}
