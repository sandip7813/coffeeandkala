<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class ArtisanCatalog
{
    /**
     * @return list<string>
     */
    public static function allowedCommands(): array
    {
        return array_values(config('artisan-runner.allowed', []));
    }

    /**
     * @return list<array{key: string, group?: string, label: string, description: string, command: string, parameters?: array<string, mixed>, danger?: bool}>
     */
    public static function presets(): array
    {
        return array_values(config('artisan-runner.presets', []));
    }

    /**
     * @return array<string, array{key: string, label: string, presets: list<array<string, mixed>>}>
     */
    public static function groupedPresets(): array
    {
        /** @var array<string, string> $groupLabels */
        $groupLabels = config('artisan-runner.groups', []);
        $grouped = [];

        foreach ($groupLabels as $key => $label) {
            $grouped[$key] = [
                'key' => $key,
                'label' => $label,
                'presets' => [],
            ];
        }

        foreach (static::presets() as $preset) {
            $group = $preset['group'] ?? 'info';

            if (! isset($grouped[$group])) {
                $grouped[$group] = [
                    'key' => $group,
                    'label' => ucfirst($group),
                    'presets' => [],
                ];
            }

            $grouped[$group]['presets'][] = $preset;
        }

        return array_filter(
            $grouped,
            fn (array $group): bool => $group['presets'] !== [],
        );
    }

    /**
     * @return array{key: string, group?: string, label: string, description: string, command: string, parameters?: array<string, mixed>, danger?: bool}|null
     */
    public static function preset(string $key): ?array
    {
        foreach (static::presets() as $preset) {
            if (($preset['key'] ?? null) === $key) {
                return $preset;
            }
        }

        return null;
    }

    public static function isAllowed(string $command): bool
    {
        return in_array($command, static::allowedCommands(), true);
    }

    public static function isUnlocked(): bool
    {
        $confirmedAt = session('artisan_runner.confirmed_at');

        if (! is_int($confirmedAt) && ! is_numeric($confirmedAt)) {
            return false;
        }

        $timeout = (int) config('artisan-runner.unlock_timeout', 3600);

        return (time() - (int) $confirmedAt) <= $timeout;
    }

    public static function unlock(): void
    {
        session(['artisan_runner.confirmed_at' => time()]);
    }

    /**
     * Discover seeder class basenames under database/seeders.
     *
     * @return list<string>
     */
    public static function seeders(): array
    {
        $path = database_path('seeders');

        if (! File::isDirectory($path)) {
            return [];
        }

        return collect(File::files($path))
            ->filter(fn ($file) => $file->getExtension() === 'php')
            ->map(fn ($file) => $file->getFilenameWithoutExtension())
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Parse a free-form Artisan invocation into a command name and parameters.
     *
     * @return array{0: string, 1: array<string|int, mixed>}
     */
    public static function parse(string $raw): array
    {
        $raw = trim(preg_replace('/\s+/', ' ', $raw) ?? '');

        if ($raw === '') {
            throw ValidationException::withMessages([
                'command' => 'Please enter an Artisan command.',
            ]);
        }

        if (preg_match('/[;&|`$()<>\\\\]/', $raw) === 1) {
            throw ValidationException::withMessages([
                'command' => 'Shell metacharacters are not allowed.',
            ]);
        }

        if (str_starts_with(strtolower($raw), 'php artisan ')) {
            $raw = substr($raw, strlen('php artisan '));
        } elseif (str_starts_with(strtolower($raw), 'artisan ')) {
            $raw = substr($raw, strlen('artisan '));
        }

        $tokens = str_getcsv($raw, ' ', '"', '\\');
        $tokens = array_values(array_filter($tokens, fn ($token) => $token !== null && $token !== ''));

        if ($tokens === []) {
            throw ValidationException::withMessages([
                'command' => 'Please enter an Artisan command.',
            ]);
        }

        $command = array_shift($tokens);

        if (! is_string($command) || ! static::isAllowed($command)) {
            throw ValidationException::withMessages([
                'command' => "The command [{$command}] is not allowed. Allowed commands: ".implode(', ', static::allowedCommands()),
            ]);
        }

        $parameters = [];

        foreach ($tokens as $token) {
            if (! is_string($token)) {
                continue;
            }

            if (str_starts_with($token, '--')) {
                if (str_contains($token, '=')) {
                    [$option, $value] = explode('=', $token, 2);
                    $parameters[$option] = $value;
                } else {
                    $parameters[$token] = true;
                }

                continue;
            }

            if (str_starts_with($token, '-')) {
                $parameters[$token] = true;

                continue;
            }

            $parameters[] = $token;
        }

        if ($command === 'db:seed' && isset($parameters['--class'])) {
            static::assertAllowedSeeder((string) $parameters['--class']);
        }

        return [$command, $parameters];
    }

    public static function assertAllowedSeeder(string $class): void
    {
        $basename = class_basename(str_replace('/', '\\', $class));

        if (! in_array($basename, static::seeders(), true)) {
            throw ValidationException::withMessages([
                'command' => "The seeder [{$class}] is not available.",
            ]);
        }
    }
}
