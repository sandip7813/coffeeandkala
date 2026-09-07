<?php

namespace App\Http\Controllers\Admin;

use App\Actions\SyncArticleSections;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOurStoryRequest;
use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\Category;
use App\Models\Meta;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * The "Our Story" page's content — a singleton Article (Article::
 * TYPE_OUR_STORY, filed under Category::TYPE_OUR_STORY's one hidden
 * category), edited via its Content tab (sections) alone: no Essentials,
 * FAQs, or Page Sections, and no list/create/delete/approve — there is
 * exactly one record, provisioned on first visit if it doesn't exist yet.
 */
class OurStoryController extends Controller
{
    public function edit(): View
    {
        abort_unless(auth()->user()?->can('manage-our-story'), 403);

        $article = $this->article()->load('sections.image', 'sections.galleryImages', 'sections.videoCompanionImage');

        return view('admin.our-story.edit', [
            'article' => $article,
            'type' => Article::TYPE_OUR_STORY,
            'meta' => Meta::forPage('our-story'),
        ]);
    }

    /**
     * The Our Story page's own SEO meta title/description/keywords — kept
     * editable here too (as well as on the Meta Data screen), since Our
     * Story is otherwise entirely managed from this one screen.
     */
    public function updateMeta(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('manage-our-story'), 403);

        $data = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ]);

        Meta::forPage('our-story')->update([
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ]);

        return redirect()->route('admin.our-story.edit')->with('status', __('SEO updated.'));
    }

    public function update(UpdateOurStoryRequest $request, SyncArticleSections $syncArticleSections): RedirectResponse
    {
        $article = $this->article();

        $syncArticleSections->handle($article, $request->validated('sections', []), [], $request->user());

        return redirect()->route('admin.our-story.edit')->with('status', __('Our Story updated.'));
    }

    /**
     * Flips one section's Active/Inactive flag instantly — same mechanism
     * as the Features/Journals edit form (see admin-articles.js).
     */
    public function updateSectionStatus(Article $article, ArticleSection $section): JsonResponse
    {
        $this->assertOurStory($article);

        abort_unless(auth()->user()?->can('manage-our-story'), 403);
        abort_unless($section->article_id === $article->id, 404);

        $section->update(['is_active' => ! $section->is_active]);

        return response()->json(['is_active' => $section->is_active]);
    }

    /**
     * Persists a drag-and-drop reorder instantly — same mechanism as the
     * Features/Journals edit form (see admin-articles.js).
     */
    public function reorderSections(Article $article, Request $request): JsonResponse
    {
        $this->assertOurStory($article);

        abort_unless(auth()->user()?->can('manage-our-story'), 403);

        $data = $request->validate([
            'section_ids' => ['required', 'array', 'min:1'],
            'section_ids.*' => ['integer', 'distinct'],
        ]);

        $sectionIds = $data['section_ids'];

        abort_unless(
            $article->sections()->whereIn('id', $sectionIds)->count() === count($sectionIds),
            422,
        );

        foreach ($sectionIds as $position => $id) {
            ArticleSection::whereKey($id)->update(['sort_order' => $position]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * The single Our Story article, created (with its own single hidden
     * category) the first time this page is ever visited.
     */
    private function article(): Article
    {
        $category = Category::query()->ofType(Category::TYPE_OUR_STORY)->first() ?? Category::create([
            'title' => 'Our Story',
            'slug' => 'our-story',
            'type' => Category::TYPE_OUR_STORY,
            'status' => true,
            'sort_order' => 0,
        ]);

        return Article::query()->ofType(Article::TYPE_OUR_STORY)->first() ?? Article::create([
            'category_id' => $category->id,
            'type' => Article::TYPE_OUR_STORY,
            'title' => 'Our Story',
            'slug' => 'our-story',
            'introduction' => '',
            'editors_note' => '',
            'authors_note' => '',
            'status' => Article::STATUS_ACTIVE,
            'created_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }

    private function assertOurStory(Article $article): void
    {
        abort_unless($article->type === Article::TYPE_OUR_STORY, 404);
    }
}
