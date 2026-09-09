<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Category;
use App\Models\MediaFile;
use App\Models\Permission;
use App\Models\Poem;
use App\Models\Role;
use App\Models\User;

class DashboardStats
{
    /**
     * Builds the dashboard payload for the given user — every stat card and
     * panel is included only when the user holds the permission that guards
     * its underlying resource, so an editor/viewer with a narrower role sees
     * a smaller, honest dashboard rather than counts they can't drill into.
     *
     * All counts and "recent" panels come from the real, DB-backed models
     * (Article/Poem/Category) — the same source the admin CRUD screens and
     * the public site read from — never from the static Feature/Journal/
     * PoetryCatalog fixtures, which only supply chapter chrome (name, icon,
     * lead copy) and would otherwise show placeholder counts that don't
     * match what's actually published.
     *
     * Every link this returns stays inside the admin panel (no frontend
     * URLs): stat cards and breakdown rows point at the relevant admin
     * index — pre-filtered by category where that makes sense — and each
     * "recent" item links straight to that record's admin edit page (or,
     * for a user without edit rights, the admin index instead).
     *
     * @return array{
     *     stats: list<array{key: string, label: string, icon: string, value: int, route: ?string}>,
     *     feature_breakdown: ?list<array{name: string, articles: int, href: string}>,
     *     journal_breakdown: ?list<array{name: string, articles: int, href: string}>,
     *     recent_journal: ?list<array{title: string, tag: string, date_label: string, href: string}>,
     *     recent_features: ?list<array{title: string, tag: string, date_label: string, href: string}>,
     *     recent_poetry: ?list<array{title: string, date_label: string, href: string}>,
     * }
     */
    public static function collect(User $user): array
    {
        $canViewGallery = $user->can('view-gallery');
        $canViewStudio = $user->can('view-studio');
        $canViewJournals = $user->can('view-journals');
        $canViewFeatures = $user->can('view-features');
        $canViewPoetry = $user->can('view-poetry');
        $canManageUsers = $user->can('manage-users');
        $canManageRoles = $user->can('manage-roles');
        $canManagePermissions = $user->can('manage-permissions');

        $stats = [];

        if ($canViewGallery) {
            $stats[] = [
                'key' => 'gallery_plates',
                'label' => 'Gallery plates',
                'icon' => 'bi-images',
                'value' => MediaFile::query()->ofType(MediaFile::TYPE_GALLERY)->active()->count(),
                'route' => 'admin.gallery.index',
            ];
        }

        if ($canViewStudio) {
            $stats[] = [
                'key' => 'studio_works',
                'label' => 'Studio works',
                'icon' => 'bi-palette',
                'value' => MediaFile::query()->ofType(MediaFile::TYPE_STUDIO)->active()->count(),
                'route' => 'admin.studio.index',
            ];
        }

        if ($canViewJournals) {
            $stats[] = [
                'key' => 'journal_entries',
                'label' => 'Journal entries',
                'icon' => 'bi-journal-richtext',
                'value' => Article::query()->ofType(Article::TYPE_JOURNAL)->active()->count(),
                'route' => 'admin.journals.index',
            ];
            $stats[] = [
                'key' => 'journal_categories',
                'label' => 'Journal categories',
                'icon' => 'bi-tags',
                'value' => Category::query()->ofType(Category::TYPE_JOURNAL)->active()->count(),
                'route' => 'admin.journals.index',
            ];
        }

        if ($canViewFeatures) {
            $stats[] = [
                'key' => 'feature_articles',
                'label' => 'Feature articles',
                'icon' => 'bi-bookmark-star',
                'value' => Article::query()->ofType(Article::TYPE_FEATURE)->active()->count(),
                'route' => 'admin.features.index',
            ];
            $stats[] = [
                'key' => 'feature_categories',
                'label' => 'Feature chapters',
                'icon' => 'bi-collection',
                'value' => Category::query()->ofType(Category::TYPE_FEATURE)->active()->count(),
                'route' => 'admin.features.index',
            ];
        }

        if ($canViewPoetry) {
            $stats[] = [
                'key' => 'poems',
                'label' => 'Poems',
                'icon' => 'bi-feather',
                'value' => Poem::query()->active()->count(),
                'route' => 'admin.poetry.index',
            ];
        }

        if ($canManageUsers) {
            $stats[] = [
                'key' => 'users',
                'label' => 'Team members',
                'icon' => 'bi-people',
                'value' => User::query()->count(),
                'route' => 'admin.users.index',
            ];
        }

        if ($canManageRoles) {
            $stats[] = [
                'key' => 'roles',
                'label' => 'Roles',
                'icon' => 'bi-shield-lock',
                'value' => Role::query()->count(),
                'route' => 'admin.roles.index',
            ];
        }

        if ($canManagePermissions) {
            $stats[] = [
                'key' => 'permissions',
                'label' => 'Permissions',
                'icon' => 'bi-key',
                'value' => Permission::query()->count(),
                'route' => 'admin.permissions.index',
            ];
        }

        $canEditJournals = $user->can('edit-journals');
        $canEditFeatures = $user->can('edit-features');
        $canEditPoetry = $user->can('edit-poetry');

        return [
            'stats' => $stats,
            'feature_breakdown' => $canViewFeatures
                ? self::categoryBreakdown(Category::TYPE_FEATURE, Article::TYPE_FEATURE, 'admin.features.index')
                : null,
            'journal_breakdown' => $canViewJournals
                ? self::categoryBreakdown(Category::TYPE_JOURNAL, Article::TYPE_JOURNAL, 'admin.journals.index')
                : null,
            'recent_journal' => $canViewJournals
                ? self::recentArticles(Article::TYPE_JOURNAL, 'admin.journals', $canEditJournals)
                : null,
            'recent_features' => $canViewFeatures
                ? self::recentArticles(Article::TYPE_FEATURE, 'admin.features', $canEditFeatures)
                : null,
            'recent_poetry' => $canViewPoetry
                ? self::recentPoetry($canEditPoetry)
                : null,
        ];
    }

    /**
     * Every active category of the given type, with its real, active
     * article count — the "chapter/category breakdown" table. Each row
     * links to the admin article index, pre-filtered to that category.
     *
     * @return list<array{name: string, articles: int, href: string}>
     */
    private static function categoryBreakdown(string $categoryType, string $articleType, string $indexRoute): array
    {
        return Category::query()
            ->ofType($categoryType)
            ->active()
            ->ordered()
            ->withCount(['articles' => fn ($query) => $query->ofType($articleType)->active()])
            ->get()
            ->map(fn (Category $category): array => [
                'name' => $category->title,
                'articles' => $category->articles_count,
                'href' => route($indexRoute, ['category_id' => $category->id]),
            ])
            ->all();
    }

    /**
     * The 5 most recently published articles of the given type. Each links
     * to its admin edit page when the user can edit this type, otherwise to
     * the admin index — never to the public site.
     *
     * @return list<array{title: string, tag: string, date_label: string, href: string}>
     */
    private static function recentArticles(string $type, string $adminRoutePrefix, bool $canEdit): array
    {
        return Article::query()
            ->ofType($type)
            ->active()
            ->with('category')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Article $article): array => [
                'title' => $article->title,
                'tag' => $article->category?->title ?? '',
                'date_label' => $article->created_at->format('j M Y'),
                'href' => $canEdit
                    ? route("{$adminRoutePrefix}.edit", $article)
                    : route("{$adminRoutePrefix}.index"),
            ])
            ->all();
    }

    /**
     * The 5 most recently published poems. Each links to its admin edit
     * page when the user can edit poetry, otherwise to the admin index.
     *
     * @return list<array{title: string, date_label: string, href: string}>
     */
    private static function recentPoetry(bool $canEdit): array
    {
        return Poem::query()
            ->active()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Poem $poem): array => [
                'title' => $poem->title,
                'date_label' => $poem->created_at->format('j M Y'),
                'href' => $canEdit
                    ? route('admin.poetry.edit', $poem)
                    : route('admin.poetry.index'),
            ])
            ->all();
    }
}
