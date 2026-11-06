<?php

namespace App\Support;

class ImageProcessingMemory
{
    /**
     * Raise PHP's memory limit to the configured image-processing ceiling
     * for the rest of the request. GD holds every decoded image
     * uncompressed in memory (roughly 5 bytes per pixel), so a multi-megapixel
     * photo that is well under the upload size cap can still blow through
     * PHP's default 128M limit. The limit is only ever raised, never lowered,
     * and hosts that disable ini_set() are left untouched.
     */
    public static function raise(): void
    {
        if (! function_exists('ini_set')) {
            return;
        }

        $current = ini_get('memory_limit');
        $required = (string) config('media.processing_memory_limit');

        if ($current === false || $current === '-1' || $required === '') {
            return;
        }

        if ($required === '-1' || ini_parse_quantity($required) > ini_parse_quantity($current)) {
            ini_set('memory_limit', $required);
        }
    }
}
