<?php

namespace App\Models\Concerns;

use App\Models\SlugRedirect;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

/**
 * A record (Article/Poem) with a public URL slug:
 *
 *  - the slug is never saved empty: if none is given, it's generated from
 *    the title — and if the title has nothing a URL can use (e.g. only
 *    emoji or punctuation), from slugFallback() — made unique with a
 *    -2, -3… suffix;
 *  - whenever the slug (or, for articles, the category) changes, the old
 *    URL is remembered so it can 301 to the new one instead of 404ing.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function (self $model): void {
            if (blank($model->slug)) {
                $model->slug = $model->uniqueSlugFrom((string) $model->title);
            }
        });

        static::updated(function (self $model): void {
            $model->rememberPreviousSlug();
        });

        static::deleting(function (self $model): void {
            $model->slugRedirects()->delete();
        });
    }

    /**
     * @return MorphMany<SlugRedirect, $this>
     */
    public function slugRedirects(): MorphMany
    {
        return $this->morphMany(SlugRedirect::class, 'redirectable');
    }

    /**
     * The column (if any) that, together with the slug, makes up the
     * record's URL — slugs only need to be unique within it (e.g. an
     * article's category_id).
     */
    protected function slugScopeColumn(): ?string
    {
        return null;
    }

    /**
     * The slug base used when the title yields nothing URL-safe.
     */
    abstract protected function slugFallback(): string;

    /**
     * A slug derived from $title that no other record (in the same scope)
     * is using.
     */
    public function uniqueSlugFrom(string $title): string
    {
        $base = Str::slug($title);

        if ($base === '') {
            $base = $this->slugFallback();
        }

        $scopeColumn = $this->slugScopeColumn();
        $slug = $base;
        $suffix = 2;

        while (
            static::query()
                ->when($scopeColumn !== null, fn ($query) => $query->where($scopeColumn, $this->getAttribute($scopeColumn)))
                ->where('slug', $slug)
                ->when($this->exists, fn ($query) => $query->whereKeyNot($this->getKey()))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    private function rememberPreviousSlug(): void
    {
        $scopeColumn = $this->slugScopeColumn();
        $scopeChanged = $scopeColumn !== null && $this->wasChanged($scopeColumn);

        if (! $this->wasChanged('slug') && ! $scopeChanged) {
            return;
        }

        $oldSlug = $this->getOriginal('slug');

        if (blank($oldSlug)) {
            return;
        }

        $oldScope = $scopeColumn !== null ? $this->getOriginal($scopeColumn) : null;
        $currentScope = $scopeColumn !== null ? $this->getAttribute($scopeColumn) : null;

        // Taking back a slug this record used before — that URL is live
        // again, so it no longer needs redirecting.
        $this->slugRedirects()
            ->where('category_id', $currentScope)
            ->where('slug', $this->slug)
            ->delete();

        // The old URL now belongs to this record; drop any older redirect
        // another record held for the same URL.
        SlugRedirect::query()
            ->where('redirectable_type', $this->getMorphClass())
            ->where('category_id', $oldScope)
            ->where('slug', $oldSlug)
            ->delete();

        $this->slugRedirects()->create([
            'category_id' => $oldScope,
            'slug' => $oldSlug,
        ]);
    }
}
