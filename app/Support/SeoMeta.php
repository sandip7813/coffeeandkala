<?php

namespace App\Support;

use App\Models\Meta;
use Illuminate\Support\Facades\App;

/**
 * Centralizes the "$meta ?? null)?->x ?: default" fallback every frontend
 * page needs for its <title>, meta description, and meta keywords —
 * `@seo($meta ?? null, $title, $description, $keywords)` sets all three
 * page sections in one call instead of three separately repeated in each
 * view (see the `@seo` Blade directive registered in AppServiceProvider).
 */
class SeoMeta
{
    public static function set(?Meta $meta, string $title, string $description, string $keywords): void
    {
        $view = App::make('view');

        $view->startSection('title', filled($meta?->title) ? $meta->title : $title);
        $view->startSection('meta_description', filled($meta?->description) ? $meta->description : $description);
        $view->startSection('meta_keywords', filled($meta?->keywords) ? $meta->keywords : $keywords);
    }
}
