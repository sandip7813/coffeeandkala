<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base validation for uploading a new Gallery or Studio media file.
 * Extended per-type (see StoreGalleryFileRequest / StoreStudioFileRequest) so
 * the media type used for the permission check and the upload rules
 * (formats, max size) can be resolved without a route parameter.
 */
abstract class StoreMediaFileRequest extends FormRequest
{
    abstract public function type(): string;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can("upload-{$this->type()}") === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $formats = implode(',', config("media.{$this->type()}.formats"));
        $maxSizeKb = config("media.{$this->type()}.max_size_kb");

        return [
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],
        ];
    }
}
