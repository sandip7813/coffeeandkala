<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\MediaFile;
use App\Models\Meta;
use App\Models\Poem;
use App\Support\ArticleCard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    private const MIN_QUERY_LENGTH = 3;

    private const SUGGEST_PER_SECTION = 4;

    private const RESULTS_PER_PAGE = 12;

    /**
     * Categorized, title-only autocomplete suggestions for the header
     * search box — a small fixed number of matches per section, with the
     * full result counts so the dropdown can link to "view all" on the
     * search results page.
     */
    public function suggest(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < self::MIN_QUERY_LENGTH) {
            return response()->json(['query' => $term, 'sections' => []]);
        }

        $sections = collect([
            $this->journalSection($term, self::SUGGEST_PER_SECTION),
            $this->featureSection($term, self::SUGGEST_PER_SECTION),
            $this->poetrySection($term, self::SUGGEST_PER_SECTION),
            $this->gallerySection($term, self::SUGGEST_PER_SECTION),
            $this->studioSection($term, self::SUGGEST_PER_SECTION),
        ])->filter(fn (array $section): bool => $section['total'] > 0)->values();

        return response()->json(['query' => $term, 'sections' => $sections]);
    }

    /**
     * The full, categorized, paginated search results page — each section
     * paginated independently via its own page query-string key.
     */
    public function index(Request $request): View
    {
        $term = trim((string) $request->query('q', ''));
        $hasTerm = $term !== '';

        return view('frontend.search', [
            'query' => $term,
            'journal' => $hasTerm
                ? $this->paginate($request, 'page_journal', $this->journalQuery($term))
                    ->through(fn (Article $article): array => $this->articleSuggestion($article, 'journal.article'))
                : null,
            'features' => $hasTerm
                ? $this->paginate($request, 'page_features', $this->featureQuery($term))
                    ->through(fn (Article $article): array => $this->articleSuggestion($article, 'features.article'))
                : null,
            'poetry' => $hasTerm
                ? $this->paginate($request, 'page_poetry', $this->poetryQuery($term))
                    ->through(fn (Poem $poem): array => $this->poemSuggestion($poem))
                : null,
            'gallery' => $hasTerm
                ? $this->paginate($request, 'page_gallery', $this->galleryQuery($term))
                    ->through(fn (MediaFile $media): array => $this->mediaSuggestion($media, 'gallery'))
                : null,
            'studio' => $hasTerm
                ? $this->paginate($request, 'page_studio', $this->studioQuery($term))
                    ->through(fn (MediaFile $media): array => $this->mediaSuggestion($media, 'studio'))
                : null,
            'meta' => Meta::forPage('search'),
        ]);
    }

    private function journalQuery(string $term)
    {
        return Article::query()
            ->ofType(Article::TYPE_JOURNAL)
            ->active()
            ->where('title', 'like', '%'.$term.'%')
            ->with('featuredImage', 'category')
            ->latest();
    }

    private function featureQuery(string $term)
    {
        return Article::query()
            ->ofType(Article::TYPE_FEATURE)
            ->active()
            ->where('title', 'like', '%'.$term.'%')
            ->with('featuredImage', 'category')
            ->latest();
    }

    private function poetryQuery(string $term)
    {
        return Poem::query()
            ->active()
            ->where('title', 'like', '%'.$term.'%')
            ->with('featuredImage')
            ->latest();
    }

    private function galleryQuery(string $term)
    {
        return MediaFile::query()
            ->ofType(MediaFile::TYPE_GALLERY)
            ->active()
            ->where('title', 'like', '%'.$term.'%')
            ->latest();
    }

    private function studioQuery(string $term)
    {
        return MediaFile::query()
            ->ofType(MediaFile::TYPE_STUDIO)
            ->active()
            ->where('title', 'like', '%'.$term.'%')
            ->latest();
    }

    /**
     * @return array{key: string, label: string, total: int, items: list<array<string, mixed>>}
     */
    private function journalSection(string $term, int $limit): array
    {
        $query = $this->journalQuery($term);
        $total = $query->count();

        $items = (clone $query)->limit($limit)->get()
            ->map(fn (Article $article): array => $this->articleSuggestion($article, 'journal.article'))
            ->all();

        return ['key' => 'journal', 'label' => 'Journal', 'total' => $total, 'items' => $items];
    }

    /**
     * @return array{key: string, label: string, total: int, items: list<array<string, mixed>>}
     */
    private function featureSection(string $term, int $limit): array
    {
        $query = $this->featureQuery($term);
        $total = $query->count();

        $items = (clone $query)->limit($limit)->get()
            ->map(fn (Article $article): array => $this->articleSuggestion($article, 'features.article'))
            ->all();

        return ['key' => 'features', 'label' => 'Features', 'total' => $total, 'items' => $items];
    }

    /**
     * @return array{key: string, label: string, total: int, items: list<array<string, mixed>>}
     */
    private function poetrySection(string $term, int $limit): array
    {
        $query = $this->poetryQuery($term);
        $total = $query->count();

        $items = (clone $query)->limit($limit)->get()
            ->map(fn (Poem $poem): array => $this->poemSuggestion($poem))
            ->all();

        return ['key' => 'poetry', 'label' => 'Poetry', 'total' => $total, 'items' => $items];
    }

    /**
     * @return array{key: string, label: string, total: int, items: list<array<string, mixed>>}
     */
    private function gallerySection(string $term, int $limit): array
    {
        return $this->mediaSection($term, $limit, $this->galleryQuery($term), 'gallery', 'Gallery', 'gallery');
    }

    /**
     * @return array{key: string, label: string, total: int, items: list<array<string, mixed>>}
     */
    private function studioSection(string $term, int $limit): array
    {
        return $this->mediaSection($term, $limit, $this->studioQuery($term), 'studio', 'Studio', 'studio');
    }

    /**
     * @return array{key: string, label: string, total: int, items: list<array<string, mixed>>}
     */
    private function mediaSection(string $term, int $limit, $query, string $key, string $label, string $indexRoute): array
    {
        $total = $query->count();

        $items = (clone $query)->limit($limit)->get()
            ->map(fn (MediaFile $media): array => $this->mediaSuggestion($media, $indexRoute))
            ->all();

        return ['key' => $key, 'label' => $label, 'total' => $total, 'items' => $items];
    }

    /**
     * @return array<string, mixed>
     */
    private function articleSuggestion(Article $article, string $articleRoute): array
    {
        $card = ArticleCard::build($article, $articleRoute);

        return [
            'title' => $card['title'],
            'description' => Str::limit($card['excerpt'], 90),
            'thumbnail' => $card['thumbnail'],
            'url' => $card['href'],
            'target' => 'link',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function poemSuggestion(Poem $poem): array
    {
        return [
            'title' => $poem->title,
            'description' => Str::limit(trim(strtok($poem->body, "\n")) ?: '', 90),
            'thumbnail' => $poem->featuredImage?->thumbnail_url,
            'url' => route('poetry.show', $poem->slug),
            'target' => 'link',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mediaSuggestion(MediaFile $media, string $indexRoute): array
    {
        return [
            'title' => $media->title,
            'description' => Str::limit((string) $media->caption, 90),
            'thumbnail' => $media->thumbnail_url,
            'url' => route($indexRoute, ['item' => $media->uuid]),
            'target' => 'lightbox',
        ];
    }

    private function paginate(Request $request, string $pageName, $query): LengthAwarePaginator
    {
        return $query->paginate(self::RESULTS_PER_PAGE, ['*'], $pageName)->withQueryString();
    }
}
