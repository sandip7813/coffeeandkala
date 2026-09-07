<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePoemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit-poetry') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $formats = implode(',', config('media.poetry.formats'));
        $maxSizeKb = config('media.poetry.max_size_kb');

        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            // Nullable — an edit can keep the current featured image.
            'featured_image' => ['nullable', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ];
    }
}
