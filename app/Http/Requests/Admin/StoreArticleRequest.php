<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base validation for creating a new Feature/Journal article. Extended per
 * type (see StoreFeatureArticleRequest / StoreJournalArticleRequest) so the
 * article type used for the permission check, the category scoping, and the
 * upload rules can be resolved without a route parameter.
 *
 * The Essentials tab fields are mandatory; the Content tab (sections) and
 * FAQs are entirely optional — an article's body can be filled in later.
 */
abstract class StoreArticleRequest extends FormRequest
{
    abstract public function type(): string;

    public function authorize(): bool
    {
        return $this->user()?->can("create-{$this->type()}") === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $formats = implode(',', config("media.{$this->type()}.formats"));
        $maxSizeKb = config("media.{$this->type()}.max_size_kb");

        return [
            'save_action' => ['nullable', 'in:draft,submit'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'introduction' => ['required', 'string'],
            'featured_image' => ['required', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],
            'editors_note' => ['required', 'string'],
            'authors_note' => ['required', 'string'],

            'sections' => ['nullable', 'array'],
            'sections.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.media_type' => ['nullable', 'in:image,video,gallery'],
            'sections.*.is_active' => ['nullable', 'boolean'],
            'sections.*.content' => ['nullable', 'string'],
            'sections.*.image_position' => ['nullable', 'in:left,right,center'],
            'sections.*.content_position' => ['nullable', 'in:beside,standalone'],
            'sections.*.image' => ['nullable', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],
            'sections.*.image_caption' => ['nullable', 'string', 'max:255'],
            'sections.*.youtube_url' => ['nullable', 'url'],
            'sections.*.youtube_position' => ['nullable', 'in:left,right'],
            'sections.*.video_companion_type' => ['nullable', 'in:text,image,none'],
            'sections.*.video_companion_image' => ['nullable', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],
            'sections.*.video_companion_image_caption' => ['nullable', 'string', 'max:255'],

            // Each Gallery Images slot carries its own optional file +
            // caption, so every image can have a distinct caption.
            'sections.*.gallery' => ['nullable', 'array', 'max:4'],
            'sections.*.gallery.*.image' => ['nullable', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],
            'sections.*.gallery.*.caption' => ['nullable', 'string', 'max:255'],

            'faqs' => ['nullable', 'array'],
            'faqs.*.question' => ['nullable', 'string', 'max:255'],
            'faqs.*.answer' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sections.*.gallery.max' => __('You can upload at most 4 new gallery images per edit.'),
        ];
    }
}
