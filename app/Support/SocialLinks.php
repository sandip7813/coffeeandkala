<?php

namespace App\Support;

use App\Models\Setting;

class SocialLinks
{
    public const SETTING_KEY = 'social_links';

    /**
     * @return array<string, array{label: string, admin_icon: string, icon: string, placeholder: string}>
     */
    public static function networks(): array
    {
        /** @var array<string, array{label: string, admin_icon: string, icon: string, placeholder: string}> $networks */
        $networks = config('social.networks', []);

        return $networks;
    }

    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        /** @var array<string, string> $stored */
        $stored = Setting::getJson(static::SETTING_KEY, []);
        $links = [];

        foreach (array_keys(static::networks()) as $key) {
            $url = trim((string) ($stored[$key] ?? ''));
            $links[$key] = $url;
        }

        return $links;
    }

    /**
     * Networks that have a non-empty URL, ready for the public footer.
     *
     * @return list<array{key: string, label: string, icon: string, url: string}>
     */
    public static function filled(): array
    {
        $networks = static::networks();
        $filled = [];

        foreach (static::all() as $key => $url) {
            if ($url === '' || ! isset($networks[$key])) {
                continue;
            }

            $filled[] = [
                'key' => $key,
                'label' => $networks[$key]['label'],
                'icon' => $networks[$key]['icon'],
                'url' => $url,
            ];
        }

        return $filled;
    }

    /**
     * @param  array<string, string|null>  $links
     */
    public static function set(array $links): void
    {
        $normalized = [];

        foreach (array_keys(static::networks()) as $key) {
            $url = trim((string) ($links[$key] ?? ''));
            $normalized[$key] = $url;
        }

        Setting::setJson(static::SETTING_KEY, $normalized);
    }
}
