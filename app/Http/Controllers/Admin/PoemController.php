<?php

namespace App\Http\Controllers\Admin;

use App\Actions\StoreMediaFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePoemRequest;
use App\Http\Requests\Admin\UpdatePoemRequest;
use App\Models\Meta;
use App\Models\Poem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Poetry management — a poem is its own record (title + body + one
 * featured image, via the polymorphic media_files.mediable_* columns),
 * with the same pending/active/inactive + approval workflow as Gallery/
 * Studio uploads.
 */
class PoemController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('view-poetry'), 403);

        $filters = $request->only(['title', 'status']);

        $poems = Poem::query()
            ->with('featuredImage', 'creator', 'approver')
            ->when(filled($filters['title'] ?? null), fn ($query) => $query->where('title', 'like', '%'.$filters['title'].'%'))
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $hasActiveFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();

        $meta = Meta::forPage('poetry');

        return view('admin.poetry.index', compact('poems', 'filters', 'hasActiveFilters', 'meta'));
    }

    /**
     * The Poetry page's own SEO meta title/description/keywords — kept
     * editable here too (as well as on the Meta Data screen). Gated on
     * 'edit-poetry' (the same permission that already governs this page's
     * own edit actions) rather than 'manage-meta', so anyone who can manage
     * Poetry at all can also reach its SEO tab.
     */
    public function updateMeta(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('edit-poetry'), 403);

        $data = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ]);

        Meta::forPage('poetry')->update([
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ]);

        return redirect()->route('admin.poetry.index')->with('status', __('SEO updated.'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()?->can('upload-poetry'), 403);

        return view('admin.poetry.create');
    }

    public function store(StorePoemRequest $request, StoreMediaFile $storeMediaFile): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $canApprove = $user->can('approve-poetry');

        $poem = Poem::create([
            'title' => $data['title'],
            'slug' => $this->slugFor($data['title']),
            'body' => $data['body'],
            'status' => $canApprove ? Poem::STATUS_ACTIVE : Poem::STATUS_PENDING,
            'created_by' => $user->id,
            'approved_by' => $canApprove ? $user->id : null,
            'approved_at' => $canApprove ? now() : null,
        ]);

        $storeMediaFile->handleForMediable(
            'poetry', $data['featured_image'], $user, Poem::class, $poem->id, 'featured',
        );

        return redirect()->route('admin.poetry.index')->with('status', __('Poem created.'));
    }

    public function edit(Poem $poem): View
    {
        abort_unless(auth()->user()?->can('edit-poetry'), 403);

        $poem->load('featuredImage');

        $meta = $poem->meta()->firstOrCreate([]);

        return view('admin.poetry.edit', compact('poem', 'meta'));
    }

    public function update(UpdatePoemRequest $request, Poem $poem, StoreMediaFile $storeMediaFile): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $slug = $data['title'] !== $poem->title
            ? $this->slugFor($data['title'], $poem->id)
            : $poem->slug;

        $poem->update([
            'title' => $data['title'],
            'slug' => $slug,
            'body' => $data['body'],
        ]);

        if (! empty($data['featured_image'])) {
            $existing = $poem->featuredImage()->first();

            if ($existing) {
                $storeMediaFile->deleteFiles($existing);
                $existing->delete();
            }

            $storeMediaFile->handleForMediable(
                'poetry', $data['featured_image'], $user, Poem::class, $poem->id, 'featured',
            );
        }

        $poem->meta()->firstOrCreate([])->update([
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ]);

        return redirect()->route('admin.poetry.index')->with('status', __('Poem updated.'));
    }

    public function updateStatus(Poem $poem): RedirectResponse
    {
        abort_unless(auth()->user()?->can('change-poetry-status'), 403);

        abort_if($poem->status === Poem::STATUS_PENDING, 422);

        $poem->update([
            'status' => $poem->status === Poem::STATUS_ACTIVE
                ? Poem::STATUS_INACTIVE
                : Poem::STATUS_ACTIVE,
        ]);

        return redirect()->route('admin.poetry.index', request()->query())
            ->with('status', __('Status updated.'));
    }

    public function approve(Poem $poem): RedirectResponse
    {
        abort_unless(auth()->user()?->can('approve-poetry'), 403);

        $poem->update([
            'status' => Poem::STATUS_ACTIVE,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.poetry.index', request()->query())
            ->with('status', __('Approved.'));
    }

    public function destroy(Poem $poem, StoreMediaFile $storeMediaFile): RedirectResponse
    {
        abort_unless(auth()->user()?->can('delete-poetry'), 403);

        $featuredImage = $poem->featuredImage()->first();

        if ($featuredImage) {
            $storeMediaFile->deleteFiles($featuredImage);
            $featuredImage->delete();
        }

        $poem->delete();

        return redirect()->route('admin.poetry.index')->with('status', __('Deleted.'));
    }

    /**
     * A unique slug for the poem, derived from its title.
     */
    private function slugFor(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 2;

        while (
            Poem::query()
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
