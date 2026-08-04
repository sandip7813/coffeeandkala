<?php

namespace App\Support;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class DashboardStats
{
    /**
     * @return array{
     *     gallery_plates: int,
     *     studio_works: int,
     *     journal_entries: int,
     *     feature_categories: int,
     *     feature_articles: int,
     *     users: int,
     *     roles: int,
     *     permissions: int,
     *     feature_breakdown: list<array{id: string, name: string, articles: int}>,
     *     recent_journal: list<array{title: string, tag: string, date_label: string}>,
     * }
     */
    public static function collect(): array
    {
        $categories = FeatureCatalog::all();

        return [
            'gallery_plates' => GalleryCatalog::count(),
            'studio_works' => StudioCatalog::count(),
            'journal_entries' => JournalCatalog::count(),
            'feature_categories' => count($categories),
            'feature_articles' => collect($categories)->sum(fn (array $category): int => count($category['articles'])),
            'users' => User::query()->count(),
            'roles' => Role::query()->count(),
            'permissions' => Permission::query()->count(),
            'feature_breakdown' => collect($categories)
                ->map(fn (array $category): array => [
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'articles' => count($category['articles']),
                ])
                ->values()
                ->all(),
            'recent_journal' => collect(JournalCatalog::all())
                ->sortByDesc('date')
                ->take(5)
                ->map(fn (array $entry): array => [
                    'title' => $entry['title'],
                    'tag' => $entry['tag'],
                    'date_label' => $entry['date_label'],
                ])
                ->values()
                ->all(),
        ];
    }
}
