<?php

namespace App\Http\Requests\Admin;

use App\Support\SocialLinks;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSocialLinksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-brand') === true;
    }

    protected function prepareForValidation(): void
    {
        $links = [];

        foreach (array_keys(SocialLinks::networks()) as $key) {
            $value = $this->input("links.{$key}");
            $links[$key] = is_string($value) ? trim($value) : '';
        }

        $this->merge(['links' => $links]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = ['links' => ['required', 'array']];

        foreach (array_keys(SocialLinks::networks()) as $key) {
            $rules["links.{$key}"] = ['nullable', 'string', 'url', 'max:255'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [];

        foreach (SocialLinks::networks() as $key => $network) {
            $attributes["links.{$key}"] = $network['label'].' URL';
        }

        return $attributes;
    }
}
