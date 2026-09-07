<?php

namespace App\Support;

class YoutubeEmbed
{
    /**
     * Turns any common YouTube URL shape (watch?v=, youtu.be/, already an
     * /embed/ link, with or without extra query params) into an embeddable
     * iframe src. Returns null for anything that isn't recognizably
     * YouTube, so the caller can skip rendering the embed entirely.
     */
    public static function url(?string $youtubeUrl): ?string
    {
        $id = self::id($youtubeUrl);

        return $id === null ? null : "https://www.youtube.com/embed/{$id}";
    }

    /**
     * Extracts the bare video ID from any common YouTube URL shape.
     */
    public static function id(?string $youtubeUrl): ?string
    {
        if (blank($youtubeUrl)) {
            return null;
        }

        $patterns = [
            '/youtu\.be\/([A-Za-z0-9_-]{6,})/',
            '/[?&]v=([A-Za-z0-9_-]{6,})/',
            '/youtube\.com\/embed\/([A-Za-z0-9_-]{6,})/',
            '/youtube\.com\/shorts\/([A-Za-z0-9_-]{6,})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $youtubeUrl, $matches) === 1) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * YouTube's own hosted thumbnail for the video — used as the static
     * "cover" image so the video never autoplays inline; it only starts
     * once the visitor clicks through to the modal.
     */
    public static function thumbnailUrl(?string $youtubeUrl): ?string
    {
        $id = self::id($youtubeUrl);

        return $id === null ? null : "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
    }
}
