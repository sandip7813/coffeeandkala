<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomeSectionRequest;
use App\Models\Article;
use App\Models\HomeSectionArticle;
use App\Models\Meta;
use App\Support\HomeSections;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeSectionController extends Controller
{
    public function edit(): View
    {
        // Only each section's own (already small) picks are loaded eagerly —
        // the full active-article catalogue is never rendered into the page;
        // see searchArticles() below for how the "Add" field finds articles
        // to pick, however many hundreds of them exist.
        $sections = collect(HomeSectionArticle::SECTIONS)->mapWithKeys(function (string $section): array {
            $picked = HomeSectionArticle::query()
                ->forSection($section)
                ->with('article')
                ->get()
                ->map(fn (HomeSectionArticle $pick): ?array => $pick->article === null ? null : [
                    'id' => $pick->article->id,
                    'label' => $pick->article->title,
                ])
                ->filter()
                ->values()
                ->all();

            return [$section => $picked];
        });

        return view('admin.home-sections.edit', [
            'sectionLabels' => HomeSections::LABELS,
            'sections' => $sections,
            'meta' => Meta::forPage('home'),
        ]);
    }

    /**
     * The homepage's own SEO meta title/description/keywords — kept
     * editable here too (as well as on the Meta Data screen) since this is
     * where a super admin already manages everything else about the
     * homepage.
     */
    public function updateMeta(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('manage-home-sections'), 403);

        $data = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ]);

        Meta::forPage('home')->update([
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ]);

        return redirect()->route('admin.home-sections.edit')->with('status', __('SEO updated.'));
    }

    public function update(UpdateHomeSectionRequest $request, string $section): RedirectResponse
    {
        abort_unless(in_array($section, HomeSectionArticle::SECTIONS, true), 404);

        // Belt-and-braces alongside the search endpoint's own type filter —
        // a hand-crafted request can't slip a journal entry into the
        // Features carousel (or vice versa) just because it skipped the UI.
        $orderedIds = collect($request->orderedActiveArticleIds())
            ->filter(fn (int $id): bool => array_key_exists(
                $section,
                HomeSections::labelsFor(Article::find($id)?->type ?? ''),
            ))
            ->values()
            ->all();

        DB::transaction(function () use ($section, $orderedIds): void {
            HomeSectionArticle::query()->forSection($section)->delete();

            foreach ($orderedIds as $index => $articleId) {
                HomeSectionArticle::create([
                    'section' => $section,
                    'article_id' => $articleId,
                    'sort_order' => $index,
                ]);
            }
        });

        return redirect()
            ->route('admin.home-sections.edit')
            ->with('status', HomeSections::LABELS[$section].' updated.')
            ->with('home_section', $section);
    }

    /**
     * Select2 autocomplete backing each section's "Add an article" field —
     * this is what lets the picker scale to hundreds (or thousands) of
     * articles: nothing is loaded until the admin actually searches, and
     * only a handful of matches ever come back.
     */
    public function searchArticles(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 3) {
            return response()->json(['results' => []]);
        }

        // The Features/Journal carousels only take their own article type;
        // Latest Pieces and The Selection (no/unrecognized 'section' param)
        // stay open to either.
        $typeFilter = match ($request->query('section')) {
            HomeSectionArticle::SECTION_FEATURES => Article::TYPE_FEATURE,
            HomeSectionArticle::SECTION_JOURNAL => Article::TYPE_JOURNAL,
            default => null,
        };

        $articles = Article::query()
            ->active()
            ->with('category')
            ->where('title', 'like', '%'.$term.'%')
            ->when($typeFilter, fn ($query) => $query->where('type', $typeFilter))
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $articles->map(fn (Article $article): array => [
                'id' => $article->id,
                'text' => sprintf(
                    '[%s] %s%s',
                    $article->type === Article::TYPE_JOURNAL ? 'Journal' : 'Feature',
                    $article->title,
                    $article->category ? ' — '.$article->category->title : '',
                ),
            ])->values(),
        ]);
    }
}
