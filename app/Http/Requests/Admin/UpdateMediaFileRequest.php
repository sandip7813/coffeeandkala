<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base validation for editing an existing Gallery or Studio media file's
 * title/caption. Extended per-type (see UpdateGalleryFileRequest /
 * UpdateStudioFileRequest) so the media type used for the permission check
 * can be resolved without a route parameter.
 */
abstract class UpdateMediaFileRequest extends FormRequest
{
    abstract public function type(): string;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can("edit-{$this->type()}") === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['required', 'string', 'max:255'],
        ];
    }
}
