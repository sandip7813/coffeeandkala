<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\PageOneLiner;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PageOneLinerController extends Controller
{
    public function edit(): View
    {
        $this->authorizeManage();

        $lines = collect(PageOneLiner::PAGES)
            ->mapWithKeys(fn (string $label, string $page): array => [$page => PageOneLiner::forPage($page)]);

        return view('admin.page-one-liners.edit', [
            'pages' => PageOneLiner::PAGES,
            'lines' => $lines,
            'categoryGroups' => [
                'Feature Categories' => $this->categoriesOfType(Category::TYPE_FEATURE),
                'Journal Categories' => $this->categoriesOfType(Category::TYPE_JOURNAL),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeManage();

        $request->merge([
            'lines' => $this->normalizeLineBreaks($request->input('lines', [])),
            'category_lines' => $this->normalizeLineBreaks($request->input('category_lines', [])),
        ]);

        $rules = [];

        foreach (array_keys(PageOneLiner::PAGES) as $page) {
            $rules["lines.$page"] = ['nullable', 'string', 'max:'.PageOneLiner::MAX_LENGTH];
        }

        $categories = Category::query()
            ->whereIn('type', [Category::TYPE_FEATURE, Category::TYPE_JOURNAL])
            ->pluck('id');

        foreach ($categories as $id) {
            $rules["category_lines.$id"] = ['nullable', 'string', 'max:'.PageOneLiner::MAX_LENGTH];
        }

        $data = $request->validate($rules);

        foreach ($categories as $id) {
            if (array_key_exists($id, $data['category_lines'] ?? [])) {
                Category::query()->whereKey($id)->update([
                    'closing_line' => PageOneLiner::clean($data['category_lines'][$id]),
                ]);
            }
        }

        foreach (array_keys(PageOneLiner::PAGES) as $page) {
            PageOneLiner::save($page, $data['lines'][$page] ?? null);
        }

        return redirect()->route('admin.page-one-liners.edit')
            ->with('status', __('Closing one-liners saved.'));
    }

    /**
     * @return Collection<int, Category>
     */
    private function categoriesOfType(string $type)
    {
        return Category::query()->ofType($type)->orderBy('sort_order')->orderBy('title')->get();
    }

    /**
     * @param  array<int|string, mixed>  $lines
     * @return array<int|string, mixed>
     */
    private function normalizeLineBreaks(array $lines): array
    {
        return collect($lines)
            ->map(fn ($line) => is_string($line) ? str_replace("\r\n", "\n", $line) : $line)
            ->all();
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()?->can('manage-page-content'), 403);
    }
}
