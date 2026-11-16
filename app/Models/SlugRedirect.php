<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A slug a record (Article/Poem) used to have — kept so its old public URL
 * can 301 to the current one. Written by App\Models\Concerns\HasSlug.
 */
class SlugRedirect extends Model
{
    protected $fillable = [
        'redirectable_type',
        'redirectable_id',
        'category_id',
        'slug',
    ];

    public function redirectable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The active article of $type that used to live at /{category}/{slug},
     * if it's still publicly reachable (active, in an active category).
     */
    public static function activeArticleFor(string $type, int $categoryId, string $slug): ?Article
    {
        $articleId = static::lookup(Article::class, $slug, $categoryId)->value('redirectable_id');

        return $articleId === null ? null : Article::query()
            ->ofType($type)
            ->active()
            ->whereHas('category', fn (Builder $query) => $query->active())
            ->with('category')
            ->find($articleId);
    }

    /**
     * The active poem that used to live at /poetry/{slug}, if any.
     */
    public static function activePoemFor(string $slug): ?Poem
    {
        $poemId = static::lookup(Poem::class, $slug)->value('redirectable_id');

        return $poemId === null ? null : Poem::query()->active()->find($poemId);
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @return Builder<static>
     */
    private static function lookup(string $modelClass, string $slug, ?int $categoryId = null): Builder
    {
        return static::query()
            ->where('redirectable_type', (new $modelClass)->getMorphClass())
            ->where('category_id', $categoryId)
            ->where('slug', $slug)
            ->latest('id');
    }
}
