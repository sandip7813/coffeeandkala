<?php

namespace App\Http\Requests\Admin;

use App\Support\BrandLogo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-brand') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'logo' => ['required', 'string', Rule::in(array_keys(BrandLogo::options()))],
        ];
    }
}
