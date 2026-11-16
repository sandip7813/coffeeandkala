<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'subtitle' => ['nullable', 'string', 'max:200'],
            // Optional — left empty, it's generated from the title.
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', Rule::unique('poems', 'slug')->ignore($this->route('poem'))],
            'body' => ['required', 'string'],
            // Nullable — an edit can keep the current featured image.
            'featured_image' => ['nullable', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => __('The slug may only contain lowercase letters, numbers, and single hyphens between words (e.g. "the-weight-of-rain").'),
        ];
    }
}
