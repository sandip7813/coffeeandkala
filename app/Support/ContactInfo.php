<?php

namespace App\Support;

use App\Models\Setting;

class ContactInfo
{
    public const SETTING_KEY = 'contact_info';

    /**
     * @return array<string, array{label: string, admin_icon: string, icon: string, placeholder: string, type: string}>
     */
    public static function fields(): array
    {
        /** @var array<string, array{label: string, admin_icon: string, icon: string, placeholder: string, type: string}> $fields */
        $fields = config('contact.fields', []);

        return $fields;
    }

    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        /** @var array<string, string> $stored */
        $stored = Setting::getJson(static::SETTING_KEY, []);
        $values = [];

        foreach (array_keys(static::fields()) as $key) {
            $values[$key] = trim((string) ($stored[$key] ?? ''));
        }

        return $values;
    }

    /**
     * Fields that have a non-empty value, ready for the public footer.
     *
     * @return list<array{key: string, label: string, icon: string, value: string, href: string}>
     */
    public static function filled(): array
    {
        $fields = static::fields();
        $filled = [];

        foreach (static::all() as $key => $value) {
            if ($value === '' || ! isset($fields[$key])) {
                continue;
            }

            $filled[] = [
                'key' => $key,
                'label' => $fields[$key]['label'],
                'icon' => $fields[$key]['icon'],
                'value' => $value,
                'href' => static::hrefFor($key, $value),
            ];
        }

        return $filled;
    }

    /**
     * The clickable destination for a contact value — mailto/tel/maps as appropriate.
     */
    protected static function hrefFor(string $key, string $value): string
    {
        return match ($key) {
            'email' => 'mailto:'.$value,
            'phone' => 'tel:'.preg_replace('/[^\d+]/', '', $value),
            'address' => 'https://www.google.com/maps/search/?api=1&query='.urlencode($value),
            default => '#',
        };
    }

    /**
     * @param  array<string, string|null>  $values
     */
    public static function set(array $values): void
    {
        $normalized = [];

        foreach (array_keys(static::fields()) as $key) {
            $normalized[$key] = trim((string) ($values[$key] ?? ''));
        }

        Setting::setJson(static::SETTING_KEY, $normalized);
    }
}
