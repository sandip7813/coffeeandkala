<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Setting;

/**
 * The short closing one-liner shown near the bottom of a frontend page —
 * admin-managed, and simply absent (null) until an admin writes one. Static
 * pages keep theirs in Settings; Feature/Journal category pages keep theirs
 * on the category itself.
 */
class PageOneLiner
{
    public const MAX_LENGTH = 150;

    /**
     * @var array<string, string>
     */
    public const PAGES = [
        'our-story' => 'Our Story',
        'features' => 'Features',
        'journal' => 'Journal',
        'studio' => 'Studio',
        'gallery' => 'Gallery',
        'poetry' => 'Poetry',
    ];

    public static function forPage(string $page): ?string
    {
        return self::clean(Setting::getValue(self::settingKey($page)));
    }

    public static function forCategory(string $type, string $slug): ?string
    {
        return self::clean(
            Category::query()->ofType($type)->where('slug', $slug)->value('closing_line')
        );
    }

    public static function save(string $page, ?string $text): void
    {
        Setting::setValue(self::settingKey($page), self::clean($text));
    }

    public static function settingKey(string $page): string
    {
        return 'page_one_liner.'.$page;
    }

    public static function clean(?string $text): ?string
    {
        $text = trim(str_replace("\r\n", "\n", (string) $text));

        return $text === '' ? null : $text;
    }
}
