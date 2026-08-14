<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeManage();

        $filters = $request->only(['title', 'type', 'status']);

        $categories = Category::query()
            ->when(filled($filters['title'] ?? null), fn ($query) => $query->where('title', 'like', '%'.$filters['title'].'%'))
            ->when(filled($filters['type'] ?? null), fn ($query) => $query->where('type', $filters['type']))
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status'] === 'active'))
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        $hasActiveFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();

        return view('admin.categories.index', compact('categories', 'filters', 'hasActiveFilters'));
    }

    public function updateStatus(Category $category): RedirectResponse
    {
        $this->authorizeManage();

        $category->update(['status' => ! $category->status]);

        return redirect()->route('admin.categories.index', request()->query())
            ->with('status', __('Category status updated.'));
    }

    public function edit(Category $category): View
    {
        $this->authorizeManage();

        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('status', __('Category updated.'));
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()?->can('manage-categories'), 403);
    }
}
