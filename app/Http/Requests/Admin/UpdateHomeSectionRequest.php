<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeSectionRequest extends FormRequest
{
    /**
     * Authorization is handled by the 'can:manage-home-sections' route
     * middleware.
     */
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
            'article_ids' => ['array'],
            'article_ids.*' => ['integer', 'distinct', 'exists:articles,id'],
        ];
    }

    /**
     * The picked article ids, in the order the admin arranged them — only
     * still-active articles are kept, so a since-deactivated pick can never
     * silently persist.
     *
     * @return list<int>
     */
    public function orderedActiveArticleIds(): array
    {
        // Cast every id to int up front — form submissions arrive as
        // strings, and the strict array_search below needs matching types.
        $ids = array_map('intval', $this->validated('article_ids', []));

        return Article::query()
            ->whereIn('id', $ids)
            ->where('status', Article::STATUS_ACTIVE)
            ->pluck('id')
            ->sortBy(fn (int $id): int => array_search($id, $ids, true))
            ->values()
            ->all();
    }
}
