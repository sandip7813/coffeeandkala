<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Base validation for editing an existing Feature/Journal article. Extended
 * per type (see UpdateFeatureArticleRequest / UpdateJournalArticleRequest)
 * so the article type used for the permission check and upload rules can be
 * resolved without a route parameter.
 *
 * The Essentials tab fields are mandatory (the featured image is exempt —
 * the existing one is kept when no replacement is uploaded); the Content
 * tab (sections) and FAQs stay optional. Sections/faqs may additionally
 * carry an 'id' to update in place.
 */
abstract class UpdateArticleRequest extends FormRequest
{
    abstract public function type(): string;

    public function authorize(): bool
    {
        return $this->user()?->can("edit-{$this->type()}") === true;
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
            'featured_image' => ['nullable', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],
            'editors_note' => ['required', 'string'],
            'authors_note' => ['required', 'string'],

            'sections' => ['nullable', 'array'],
            'sections.*.id' => ['nullable', 'integer', 'exists:article_sections,id'],
            'sections.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.media_type' => ['nullable', 'in:image,video,gallery'],
            'sections.*.is_active' => ['nullable', 'boolean'],
            'sections.*.content' => ['nullable', 'string'],
            'sections.*.image_position' => ['nullable', 'in:left,right,center'],
            'sections.*.content_position' => ['nullable', 'in:beside,standalone'],
            'sections.*.image' => ['nullable', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],
            'sections.*.image_caption' => ['nullable', 'string', 'max:255'],
            'sections.*.remove_image' => ['nullable', 'boolean'],
            'sections.*.youtube_url' => ['nullable', 'url'],
            'sections.*.youtube_position' => ['nullable', 'in:left,right'],
            'sections.*.video_companion_type' => ['nullable', 'in:text,image,none'],
            'sections.*.video_companion_image' => ['nullable', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],
            'sections.*.video_companion_image_caption' => ['nullable', 'string', 'max:255'],
            'sections.*.remove_video_companion_image' => ['nullable', 'boolean'],

            // A section's total gallery isn't capped — each Gallery Images
            // slot carries its own optional id (update in place), file
            // (new/replacement upload), and caption. What IS capped, in
            // withValidator() below, is how many brand-new slots (no id —
            // not yet saved) a single edit can add at once.
            'sections.*.gallery' => ['nullable', 'array'],
            'sections.*.gallery.*.id' => ['nullable', 'integer', 'exists:media_files,id'],
            'sections.*.gallery.*.image' => ['nullable', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],
            'sections.*.gallery.*.caption' => ['nullable', 'string', 'max:255'],

            'faqs' => ['nullable', 'array'],
            'faqs.*.id' => ['nullable', 'integer', 'exists:article_faqs,id'],
            'faqs.*.question' => ['nullable', 'string', 'max:255'],
            'faqs.*.answer' => ['nullable', 'string'],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Caps new gallery uploads at 4 per edit, without limiting the
     * section's total gallery size — an already-large gallery just can't
     * grow by more than 4 images in a single save.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('sections', []) as $index => $section) {
                $newSlotCount = collect($section['gallery'] ?? [])
                    ->filter(fn (array $slot): bool => empty($slot['id']))
                    ->count();

                if ($newSlotCount > 4) {
                    $validator->errors()->add(
                        "sections.{$index}.gallery",
                        __('You can upload at most 4 new gallery images per edit.'),
                    );
                }
            }
        });
    }
}
