<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePoemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('upload-poetry') === true;
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
            'featured_image' => ['required', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],
        ];
    }
}
