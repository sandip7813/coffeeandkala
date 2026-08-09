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

        return $user?->can('manage-artisan') === true
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
            'type' => ['nullable', 'string', Rule::in(['artisan', 'composer'])],
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
     * @return array{0: string, 1: string, 2: array<string|int, mixed>}|null
     */
    public function resolvedInvocation(): ?array
    {
        $presetKey = $this->string('preset')->toString();

        if ($presetKey !== '') {
            $preset = ArtisanCatalog::preset($presetKey);

            if ($preset === null) {
                return null;
            }

            $type = ArtisanCatalog::presetType($preset);
            $parameters = $preset['parameters'] ?? [];

            if ($type === 'artisan' && $preset['command'] === 'db:seed' && $this->filled('seeder')) {
                $parameters['--class'] = $this->string('seeder')->toString();
            }

            return [$type, $preset['command'], $parameters];
        }

        $command = $this->string('command')->toString();

        if ($command === '') {
            return null;
        }

        $type = $this->string('type')->toString() ?: 'artisan';

        if ($type === 'composer') {
            return ['composer', ...ArtisanCatalog::parseComposer($command)];
        }

        return ['artisan', ...ArtisanCatalog::parse($command)];
    }
}
