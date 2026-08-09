<?php

namespace App\Actions;

use App\Support\ArtisanCatalog;
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

        $process = new Process([$binary, $command, ...$arguments], base_path(), null, null, $timeout > 0 ? $timeout : null);

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
}
