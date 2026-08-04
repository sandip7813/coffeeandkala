<?php

namespace App\Http\Requests\Admin;

use App\Support\ArtisanCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RunArtisanCommandRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && ($user->isSuperAdmin() || $user->hasPermission('manage-settings'))
            && ArtisanCatalog::isUnlocked();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'preset' => ['nullable', 'string', 'max:100'],
            'command' => ['nullable', 'string', 'max:500'],
            'seeder' => ['nullable', 'string', Rule::in(ArtisanCatalog::seeders())],
            'confirm' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'confirm.accepted' => 'Confirm the warning dialog before running this command.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $preset = $this->string('preset')->toString();
            $command = $this->string('command')->toString();

            if ($preset === '' && $command === '') {
                $validator->errors()->add('command', 'Choose a quick action or enter a command.');

                return;
            }

            if ($preset !== '' && ArtisanCatalog::preset($preset) === null) {
                $validator->errors()->add('preset', 'Unknown quick action.');
            }
        });
    }

    /**
     * @return array{0: string, 1: array<string|int, mixed>}|null
     */
    public function resolvedInvocation(): ?array
    {
        $presetKey = $this->string('preset')->toString();

        if ($presetKey !== '') {
            $preset = ArtisanCatalog::preset($presetKey);

            if ($preset === null) {
                return null;
            }

            $parameters = $preset['parameters'] ?? [];

            if ($preset['command'] === 'db:seed' && $this->filled('seeder')) {
                $parameters['--class'] = $this->string('seeder')->toString();
            }

            return [$preset['command'], $parameters];
        }

        $command = $this->string('command')->toString();

        if ($command === '') {
            return null;
        }

        return ArtisanCatalog::parse($command);
    }
}
