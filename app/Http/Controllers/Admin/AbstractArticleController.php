<?php

namespace App\Http\Controllers\Admin;

use App\Actions\StoreMediaFile;
use App\Actions\SyncArticleSections;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\UpdateArticleRequest;
use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\Category;
use App\Models\HomeSectionArticle;
use App\Models\User;
use App\Support\HomeSections;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Shared Features/Journals article management. Both sections have identical
 * functionality (create, edit, review, approve, activate/deactivate,
 * delete) and differ only in the `articles.type`/`categories.type` they
 * operate on and the permission names they check — FeatureArticleController
 * and JournalArticleController just supply those.
 */
abstract class AbstractArticleController extends Controller
{
    abstract protected function type(): string;

    /**
     * The matching Category::TYPE_* value — kept distinct from type()
     * because Article's type is deliberately plural ('features'/'journals',
     * matching routes/permissions/config) while Category's is singular.
     */
    abstract protected function categoryType(): string;

    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user?->can("view-{$this->type()}"), 403);

        $filters = $request->only(['title', 'status', 'category_id', 'created_by']);

        $articles = Article::query()
            ->ofType($this->type())
            ->with('creator', 'approver', 'category')
            ->when(filled($filters['title'] ?? null), fn ($query) => $query->where('title', 'like', '%'.$filters['title'].'%'))
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->when(filled($filters['category_id'] ?? null), fn ($query) => $query->where('category_id', $filters['category_id']))
            ->when(filled($filters['created_by'] ?? null), fn ($query) => $query->where('created_by', $filters['created_by']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $categories = Category::query()->ofType($this->categoryType())->ordered()->get();

        $hasActiveFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();

        // The creator filter is submitted as a plain user id (see the
        // Select2 autocomplete below), but the field should keep showing
        // the name the admin picked rather than just the raw id once the
        // page reloads.
        $creatorFilterLabel = null;

        if (filled($filters['created_by'] ?? null)) {
            $matchedUser = User::find($filters['created_by']);
            $creatorFilterLabel = $matchedUser?->full_name;
        }

        // Which homepage sections (if any) each listed article already
        // belongs to, for the quick toggles below — only worth the query
        // when the viewer can actually see/use them.
        $canManageHomeSections = (bool) $user?->can('manage-home-sections');
        $homeSectionMemberships = $canManageHomeSections
            ? HomeSectionArticle::query()
                ->whereIn('article_id', $articles->pluck('id'))
                ->get()
                ->groupBy('article_id')
                ->map(fn ($picks) => $picks->pluck('section')->all())
            : collect();

        return view("admin.{$this->type()}.index", compact(
            'articles', 'categories', 'filters', 'hasActiveFilters', 'creatorFilterLabel',
            'canManageHomeSections', 'homeSectionMemberships',
        ));
    }

    /**
     * Select2 autocomplete backing the "Created By" search filter — mirrors
     * UserController::search, but returns each user's real id (what
     * `articles.created_by` actually stores) rather than their email.
     */
    public function searchCreators(Request $request): JsonResponse
    {
        abort_unless($request->user()?->can("view-{$this->type()}"), 403);

        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 3) {
            return response()->json(['results' => []]);
        }

        $users = User::query()
            ->where('first_name', 'like', '%'.$term.'%')
            ->orWhere('last_name', 'like', '%'.$term.'%')
            ->orWhere('email', 'like', '%'.$term.'%')
            ->orderBy('first_name')
            ->limit(20)
            ->get(['id', 'first_name', 'last_name', 'email']);

        return response()->json([
            'results' => $users->map(fn (User $user): array => [
                'id' => $user->id,
                'text' => "{$user->full_name} ({$user->email})",
            ])->values(),
        ]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()?->can("create-{$this->type()}"), 403);

        $categories = Category::query()->ofType($this->categoryType())->active()->ordered()->get();

        return view("admin.{$this->type()}.create", ['categories' => $categories, 'article' => new Article]);
    }

    protected function storeArticle(StoreArticleRequest $request, StoreMediaFile $storeMediaFile, SyncArticleSections $syncArticleSections): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $canApprove = $user->can("approve-{$this->type()}");
        $saveAsDraft = ($data['save_action'] ?? 'submit') === 'draft';

        $article = DB::transaction(function () use ($data, $user, $canApprove, $saveAsDraft, $storeMediaFile, $syncArticleSections) {
            $article = Article::create([
                'category_id' => $data['category_id'],
                'type' => $this->type(),
                'title' => $data['title'],
                'slug' => $this->slugFor($data['category_id'], $data['title']),
                'introduction' => $data['introduction'],
                'editors_note' => $data['editors_note'],
                'authors_note' => $data['authors_note'],
                'status' => $saveAsDraft ? Article::STATUS_DRAFT : ($canApprove ? Article::STATUS_ACTIVE : Article::STATUS_PENDING),
                'created_by' => $user->id,
                'approved_by' => (! $saveAsDraft && $canApprove) ? $user->id : null,
                'approved_at' => (! $saveAsDraft && $canApprove) ? now() : null,
            ]);

            $storeMediaFile->handleForMediable(
                $this->type(), $data['featured_image'], $user, Article::class, $article->id, 'featured',
            );

            $syncArticleSections->handle($article, $data['sections'] ?? [], $data['faqs'] ?? [], $user);

            return $article;
        });

        return redirect()->route("admin.{$this->type()}.index")
            ->with('status', $saveAsDraft ? __('Saved as draft.') : __('Article created.'));
    }

    public function edit(Article $article): View
    {
        $this->assertType($article);

        abort_unless(auth()->user()?->can("edit-{$this->type()}"), 403);

        $article->load('sections.image', 'sections.galleryImages', 'sections.videoCompanionImage', 'faqs', 'featuredImage');

        $categories = Category::query()->ofType($this->categoryType())->active()->ordered()->get();

        $canManageHomeSections = (bool) auth()->user()?->can('manage-home-sections');
        $articleHomeSections = $canManageHomeSections
            ? HomeSectionArticle::query()->where('article_id', $article->id)->pluck('section')->all()
            : [];

        $meta = $article->meta()->firstOrCreate([]);

        return view("admin.{$this->type()}.edit", compact('article', 'categories', 'canManageHomeSections', 'articleHomeSections', 'meta'));
    }

    protected function updateArticle(UpdateArticleRequest $request, Article $article, StoreMediaFile $storeMediaFile, SyncArticleSections $syncArticleSections): RedirectResponse
    {
        $this->assertType($article);

        $data = $request->validated();
        $user = $request->user();

        // "Save as Draft" only has an effect while the article has never
        // been active — once approved_at is set, this silently behaves as
        // a normal save (the edit form doesn't offer the button by then
        // anyway; this is just the defensive backend-side mirror of that).
        $saveAsDraft = ($data['save_action'] ?? 'submit') === 'draft' && $article->canBeDrafted();

        DB::transaction(function () use ($data, $article, $user, $saveAsDraft, $storeMediaFile, $syncArticleSections) {
            $slug = $data['title'] !== $article->title || $data['category_id'] !== $article->category_id
                ? $this->slugFor($data['category_id'], $data['title'], $article->id)
                : $article->slug;

            $article->fill([
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'slug' => $slug,
                'introduction' => $data['introduction'],
                'editors_note' => $data['editors_note'],
                'authors_note' => $data['authors_note'],
            ]);

            if ($saveAsDraft) {
                $article->status = Article::STATUS_DRAFT;
            } elseif ($article->status === Article::STATUS_DRAFT) {
                // Leaving draft for the first time via "Submit for Review"/"Publish".
                $canApprove = $user->can("approve-{$this->type()}");
                $article->status = $canApprove ? Article::STATUS_ACTIVE : Article::STATUS_PENDING;
                $article->approved_by = $canApprove ? $user->id : null;
                $article->approved_at = $canApprove ? now() : null;
            }
            // Otherwise the article is already pending/active/inactive —
            // a plain Save never touches status; only updateStatus()/
            // approve() do.

            $article->save();

            if (! empty($data['featured_image'])) {
                $existing = $article->featuredImage()->first();

                if ($existing) {
                    $storeMediaFile->deleteFiles($existing);
                    $existing->delete();
                }

                $storeMediaFile->handleForMediable(
                    $this->type(), $data['featured_image'], $user, Article::class, $article->id, 'featured',
                );
            }

            $syncArticleSections->handle($article, $data['sections'] ?? [], $data['faqs'] ?? [], $user);

            $article->meta()->firstOrCreate([])->update([
                'title' => $data['meta_title'] ?? null,
                'description' => $data['meta_description'] ?? null,
                'keywords' => $data['meta_keywords'] ?? null,
            ]);
        });

        return redirect()->route("admin.{$this->type()}.index")
            ->with('status', $saveAsDraft ? __('Saved as draft.') : __('Article updated.'));
    }

    public function updateStatus(Article $article): RedirectResponse
    {
        $this->assertType($article);

        abort_unless(auth()->user()?->can("change-{$this->type()}-status"), 403);

        // A draft/pending article has never been active — toggling
        // active/inactive only makes sense once it has actually been
        // published at least once (see Article::canBeDrafted()).
        abort_if(in_array($article->status, [Article::STATUS_DRAFT, Article::STATUS_PENDING], true), 422);

        $article->update([
            'status' => $article->status === Article::STATUS_ACTIVE
                ? Article::STATUS_INACTIVE
                : Article::STATUS_ACTIVE,
        ]);

        return redirect()->route("admin.{$this->type()}.index", request()->query())
            ->with('status', __('Status updated.'));
    }

    public function approve(Article $article): RedirectResponse
    {
        $this->assertType($article);

        abort_unless(auth()->user()?->can("approve-{$this->type()}"), 403);

        $article->update([
            'status' => Article::STATUS_ACTIVE,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route("admin.{$this->type()}.index", request()->query())
            ->with('status', __('Approved.'));
    }

    /**
     * Flips one section's Active/Inactive flag instantly, independent of
     * the rest of the edit form — called via AJAX from the toggle on the
     * edit page (see admin-articles.js), not exposed on Add Article since
     * a section has no id to target until the article is first saved.
     */
    public function updateSectionStatus(Article $article, ArticleSection $section): JsonResponse
    {
        $this->assertType($article);

        abort_unless(auth()->user()?->can("edit-{$this->type()}"), 403);
        abort_unless($section->article_id === $article->id, 404);

        $section->update(['is_active' => ! $section->is_active]);

        return response()->json(['is_active' => $section->is_active]);
    }

    /**
     * Persists a drag-and-drop reorder instantly (see admin-articles.js) —
     * takes the full list of already-saved section ids in their new order
     * and rewrites sort_order to match. A section not yet saved (added in
     * this edit session but not submitted) has no id to include, so it
     * isn't touched here; its position is captured normally on the next
     * full form Save.
     */
    public function reorderSections(Article $article, Request $request): JsonResponse
    {
        $this->assertType($article);

        abort_unless(auth()->user()?->can("edit-{$this->type()}"), 403);

        $data = $request->validate([
            'section_ids' => ['required', 'array', 'min:1'],
            'section_ids.*' => ['integer', 'distinct'],
        ]);

        $sectionIds = $data['section_ids'];

        abort_unless(
            $article->sections()->whereIn('id', $sectionIds)->count() === count($sectionIds),
            422,
        );

        DB::transaction(function () use ($sectionIds): void {
            foreach ($sectionIds as $position => $id) {
                ArticleSection::whereKey($id)->update(['sort_order' => $position]);
            }
        });

        return response()->json(['status' => 'ok']);
    }

    /**
     * Instantly adds/removes this article from one homepage section (Latest
     * Pieces / The Selection / Features / Journal) — the same underlying
     * pick a super admin manages in bulk on the Home Page Sections screen,
     * just toggled from the article's own list/edit page. Gated on
     * 'manage-home-sections' rather than this controller's usual
     * '*-features'/'*-journals' permissions, since it's specifically that
     * permission's feature.
     */
    public function toggleHomeSection(Article $article, string $section): JsonResponse
    {
        $this->assertType($article);

        abort_unless(auth()->user()?->can('manage-home-sections'), 403);
        // Features articles can't join the Journal carousel, and vice versa —
        // Latest Pieces/The Selection stay open to either type.
        abort_unless(array_key_exists($section, HomeSections::toggleableLabelsFor($article->type)), 404);

        $existing = HomeSectionArticle::query()
            ->where('section', $section)
            ->where('article_id', $article->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json(['active' => false]);
        }

        abort_unless($article->status === Article::STATUS_ACTIVE, 422);

        $nextOrder = (int) (HomeSectionArticle::query()->forSection($section)->max('sort_order') ?? -1) + 1;

        HomeSectionArticle::create([
            'section' => $section,
            'article_id' => $article->id,
            'sort_order' => $nextOrder,
        ]);

        return response()->json(['active' => true]);
    }

    public function destroy(Article $article, StoreMediaFile $storeMediaFile): RedirectResponse
    {
        $this->assertType($article);

        abort_unless(auth()->user()?->can("delete-{$this->type()}"), 403);

        $article->load('sections.image', 'sections.galleryImages', 'sections.videoCompanionImage', 'featuredImage');

        DB::transaction(function () use ($article, $storeMediaFile) {
            $images = collect([$article->featuredImage])
                ->merge($article->sections->flatMap(fn ($section) => [$section->image, $section->videoCompanionImage])->filter())
                ->merge($article->sections->flatMap(fn ($section) => $section->galleryImages))
                ->filter();

            foreach ($images as $mediaFile) {
                $storeMediaFile->deleteFiles($mediaFile);
                $mediaFile->delete();
            }

            $article->delete();
        });

        return redirect()->route("admin.{$this->type()}.index")
            ->with('status', __('Deleted.'));
    }

    /**
     * Prevent a features URL from operating on a journal record (or vice
     * versa) when both share the same {article} route-model-bound Article.
     */
    private function assertType(Article $article): void
    {
        abort_unless($article->type === $this->type(), 404);
    }

    /**
     * A unique-per-category slug for the article, derived from its title.
     */
    private function slugFor(int $categoryId, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 2;

        while (
            Article::query()
                ->where('category_id', $categoryId)
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
