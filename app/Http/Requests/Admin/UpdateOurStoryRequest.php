<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for the Our Story singleton's Content tab — sections only, no
 * Essentials (category/title/introduction/featured image/editor's-author's
 * note), no FAQs, no Page Sections. Mirrors UpdateArticleRequest's
 * sections.* rules exactly, just without everything else that request
 * validates.
 */
class UpdateOurStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-our-story') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $formats = implode(',', config('media.'.Article::TYPE_OUR_STORY.'.formats'));
        $maxSizeKb = config('media.'.Article::TYPE_OUR_STORY.'.max_size_kb');

        return [
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

            'sections.*.gallery' => ['nullable', 'array'],
            'sections.*.gallery.*.id' => ['nullable', 'integer', 'exists:media_files,id'],
            'sections.*.gallery.*.image' => ['nullable', 'image', "mimes:{$formats}", "max:{$maxSizeKb}"],
            'sections.*.gallery.*.caption' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Caps new gallery uploads at 4 per edit, without limiting the
     * section's total gallery size — same rule as UpdateArticleRequest.
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
