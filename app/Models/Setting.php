<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever(static::cacheKey($key), function () use ($key, $default): ?string {
            $setting = static::query()->where('key', $key)->first();

            return $setting?->value ?? $default;
        });
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );

        Cache::forget(static::cacheKey($key));
    }

    /**
     * @param  array<string, mixed>  $default
     * @return array<string, mixed>
     */
    public static function getJson(string $key, array $default = []): array
    {
        $value = static::getValue($key);

        if ($value === null || $value === '') {
            return $default;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : $default;
    }

    /**
     * @param  array<string, mixed>  $value
     */
    public static function setJson(string $key, array $value): void
    {
        static::setValue($key, json_encode($value, JSON_THROW_ON_ERROR));
    }

    public static function cacheKey(string $key): string
    {
        return 'setting.'.$key;
    }
}
