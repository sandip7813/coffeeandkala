<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\Poem;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PoetryController extends Controller
{
    private const PER_PAGE = 16;

    public function index(): View
    {
        $poems = $this->poemsQuery()->paginate(self::PER_PAGE)->withQueryString();

        $offset = $poems->firstItem() ?? 1;

        /** @var LengthAwarePaginator<int, array<string, mixed>> $poems */
        $poems = $poems->through(fn (Poem $poem, int $key): array => $this->buildCard($poem, $offset - 1 + $key));

        return view('frontend.poetry', [
            'poems' => $poems,
            'meta' => Meta::forPage('poetry'),
        ]);
    }

    public function show(string $poem): View
    {
        $poems = $this->poems();
        $currentIndex = collect($poems)->search(fn (array $p): bool => $p['slug'] === $poem);

        abort_if($currentIndex === false, 404);

        $poemsWithNearby = collect($poems)
            ->map(fn (array $p): array => [...$p, 'nearby' => $this->nearby($poems, $p['slug'], 3)])
            ->all();

        $poemModel = Poem::query()->active()->where('slug', $poem)->first();

        return view('frontend.poetry-show', [
            'poems' => $poemsWithNearby,
            'current' => $poems[$currentIndex],
            'meta' => $poemModel?->meta()->firstOrCreate([]),
        ]);
    }

    /**
     * Every active poem, newest first, in the flat "door" shape the Poetry
     * views already expect — the stanzas are derived from the poem's own
     * body text (blank lines separate stanzas, single line breaks separate
     * lines within one).
     *
     * @return list<array<string, mixed>>
     */
    private function poems(): array
    {
        return $this->poemsQuery()
            ->get()
            ->values()
            ->map(fn (Poem $poem, int $index): array => $this->buildCard($poem, $index))
            ->all();
    }

    private function poemsQuery(): Builder
    {
        return Poem::query()
            ->active()
            ->with('featuredImage')
            ->latest();
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCard(Poem $poem, int $index): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($poem->body)) ?: [];

        $stanzas = [];
        $current = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                if ($current !== []) {
                    $stanzas[] = $current;
                    $current = [];
                }

                continue;
            }

            $current[] = $line;
        }

        if ($current !== []) {
            $stanzas[] = $current;
        }

        $firstLine = collect($lines)->first(fn (string $line): bool => trim($line) !== '') ?? '';

        return [
            'slug' => $poem->slug,
            'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            'title' => $poem->title,
            'excerpt' => Str::limit($firstLine, 100),
            'src' => $poem->featuredImage?->large_url,
            'thumb' => $poem->featuredImage?->thumbnail_url,
            'stanzas' => $stanzas !== [] ? $stanzas : [['']],
        ];
    }

    /**
     * The `$each` poems before and after the given slug, in reading order
     * (furthest-back first, nearest-ahead last), wrapping at either end —
     * same algorithm PoetryCatalog::nearby() used over static data.
     *
     * @param  list<array<string, mixed>>  $poems
     * @return array{prev: list<array<string, mixed>>, next: list<array<string, mixed>>}
     */
    private function nearby(array $poems, string $slug, int $each): array
    {
        $index = collect($poems)->search(fn (array $p): bool => $p['slug'] === $slug);
        $count = count($poems);
        $each = min($each, max($count - 1, 0));

        $prev = [];
        for ($i = $each; $i >= 1; $i--) {
            $prev[] = $poems[($index - $i + $count) % $count];
        }

        $next = [];
        for ($i = 1; $i <= $each; $i++) {
            $next[] = $poems[($index + $i) % $count];
        }

        return ['prev' => $prev, 'next' => $next];
    }
}
