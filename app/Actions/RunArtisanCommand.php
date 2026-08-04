<?php

namespace App\Actions;

use App\Support\ArtisanCatalog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use Throwable;

class RunArtisanCommand
{
    /**
     * @param  array<string|int, mixed>  $parameters
     * @return array{command: string, exit_code: int, output: string, succeeded: bool}
     */
    public function handle(string $command, array $parameters = []): array
    {
        if (! ArtisanCatalog::isAllowed($command)) {
            throw ValidationException::withMessages([
                'command' => "The command [{$command}] is not allowed.",
            ]);
        }

        if (in_array($command, config('artisan-runner.force', []), true)) {
            $parameters['--force'] = true;
        }

        if ($command === 'db:seed' && isset($parameters['--class'])) {
            $class = (string) $parameters['--class'];
            ArtisanCatalog::assertAllowedSeeder($class);

            if (! str_contains($class, '\\')) {
                $parameters['--class'] = 'Database\\Seeders\\'.$class;
            }
        }

        $timeout = (int) config('artisan-runner.timeout', 300);
        if ($timeout > 0) {
            set_time_limit($timeout);
        }

        $display = $this->formatDisplay($command, $parameters);

        try {
            $exitCode = Artisan::call($command, $parameters);
            $output = Artisan::output();
        } catch (Throwable $exception) {
            return [
                'command' => $display,
                'exit_code' => 1,
                'output' => $exception->getMessage(),
                'succeeded' => false,
            ];
        }

        return [
            'command' => $display,
            'exit_code' => $exitCode,
            'output' => $output !== '' ? $output : '(no output)',
            'succeeded' => $exitCode === 0,
        ];
    }

    /**
     * @param  array<string|int, mixed>  $parameters
     */
    private function formatDisplay(string $command, array $parameters): string
    {
        $parts = ['php artisan', $command];

        foreach ($parameters as $key => $value) {
            if (is_int($key)) {
                $parts[] = (string) $value;

                continue;
            }

            if ($value === true) {
                $parts[] = $key;

                continue;
            }

            if ($value === false || $value === null) {
                continue;
            }

            $parts[] = $key.'='.$value;
        }

        return implode(' ', $parts);
    }
}
