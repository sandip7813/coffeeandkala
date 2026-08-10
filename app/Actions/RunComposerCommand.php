<?php

namespace App\Actions;

use App\Support\ArtisanCatalog;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Exception\ExceptionInterface;
use Symfony\Component\Process\Process;

class RunComposerCommand
{
    /**
     * @param  list<string>  $arguments
     * @return array{command: string, exit_code: int, output: string, succeeded: bool}
     */
    public function handle(string $command, array $arguments = []): array
    {
        if (! ArtisanCatalog::isAllowedComposer($command)) {
            throw ValidationException::withMessages([
                'command' => "The composer command [{$command}] is not allowed.",
            ]);
        }

        if (in_array($command, config('artisan-runner.composer_force', []), true) && ! in_array('--no-interaction', $arguments, true)) {
            $arguments[] = '--no-interaction';
        }

        $binary = (string) config('artisan-runner.composer_binary', 'composer');
        $timeout = (int) config('artisan-runner.timeout', 300);

        $process = new Process([$binary, $command, ...$arguments], base_path(), $this->composerEnv(), null, $timeout > 0 ? $timeout : null);

        $display = $this->formatDisplay($command, $arguments);

        try {
            $process->run();
        } catch (ExceptionInterface $exception) {
            return [
                'command' => $display,
                'exit_code' => 1,
                'output' => $exception->getMessage(),
                'succeeded' => false,
            ];
        }

        $output = trim($process->getOutput().$process->getErrorOutput());

        return [
            'command' => $display,
            'exit_code' => $process->getExitCode() ?? 1,
            'output' => $output !== '' ? $output : '(no output)',
            'succeeded' => $process->isSuccessful(),
        ];
    }

    /**
     * @param  list<string>  $arguments
     */
    private function formatDisplay(string $command, array $arguments): string
    {
        return implode(' ', ['composer', $command, ...$arguments]);
    }

    /**
     * Some environments (e.g. PHP-FPM workers) don't have HOME set, which
     * makes Composer refuse to run. Point it at a dedicated, writable home
     * directory instead.
     *
     * @return array<string, string>
     */
    private function composerEnv(): array
    {
        $home = (string) config('artisan-runner.composer_home', storage_path('framework/composer-home'));

        File::ensureDirectoryExists($home);

        return [
            'COMPOSER_HOME' => $home,
            'HOME' => $home,
        ];
    }
}
