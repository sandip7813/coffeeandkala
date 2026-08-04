<?php

namespace App\Support;

use App\Models\Setting;
use InvalidArgumentException;

class BrandLogo
{
    public const SETTING_KEY = 'brand_logo';

    /**
     * @return array<string, array{label: string, description: string, path: string, email_path: string, width: int, height: int}>
     */
    public static function options(): array
    {
        /** @var array<string, array{label: string, description: string, path: string, email_path: string, width: int, height: int}> $logos */
        $logos = config('brand.logos', []);

        return $logos;
    }

    public static function defaultKey(): string
    {
        return (string) config('brand.default_logo', 'mark');
    }

    public static function currentKey(): string
    {
        $key = Setting::getValue(static::SETTING_KEY, static::defaultKey()) ?? static::defaultKey();

        return array_key_exists($key, static::options()) ? $key : static::defaultKey();
    }

    /**
     * @return array{label: string, description: string, path: string, email_path: string, width: int, height: int}
     */
    public static function current(): array
    {
        return static::options()[static::currentKey()];
    }

    public static function path(): string
    {
        return static::current()['path'];
    }

    public static function absolutePath(): string
    {
        return public_path(static::path());
    }

    public static function emailAbsolutePath(): string
    {
        return public_path(static::current()['email_path']);
    }

    public static function url(): string
    {
        return asset(static::path());
    }

    public static function width(): int
    {
        return (int) static::current()['width'];
    }

    public static function height(): int
    {
        return (int) static::current()['height'];
    }

    public static function set(string $key): void
    {
        if (! array_key_exists($key, static::options())) {
            throw new InvalidArgumentException("Unknown brand logo [{$key}].");
        }

        Setting::setValue(static::SETTING_KEY, $key);
    }

    public static function isValid(string $key): bool
    {
        return array_key_exists($key, static::options());
    }
}
