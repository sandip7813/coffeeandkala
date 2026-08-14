<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'profile_photo' => [
                'required',
                'file',
                'image',
                'mimes:'.implode(',', config('media.profile_photo.formats')),
                'max:'.config('media.profile_photo.max_size_kb'),
            ],
        ];
    }
}
