<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    public const TYPE_FEATURE = 'feature';

    public const TYPE_JOURNAL = 'journal';

    // A single hidden category the "Our Story" singleton article is filed
    // under — Our Story has no real category concept of its own (no
    // Essentials tab, no category picker), this just satisfies articles.
    // category_id. Never surfaced in any category listing (nothing queries
    // Category::ofType(self::TYPE_OUR_STORY) except OurStoryController's
    // own provisioning).
    public const TYPE_OUR_STORY = 'our_story';

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'type',
        'description',
        'sort_order',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category): void {
            $category->uuid ??= (string) Str::uuid();
        });

        // Every Feature/Journal category page gets its own SEO meta row the
        // moment it's created — the hidden Our Story category is excluded,
        // since Our Story is managed as one of the fixed static pages
        // instead (see Meta::STATIC_PAGES).
        static::created(function (Category $category): void {
            if ($category->type !== self::TYPE_OUR_STORY) {
                $category->meta()->create([]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'status' => 'boolean',
        ];
    }

    /**
     * This category page's own SEO meta data (title/description/keywords).
     */
    public function meta(): MorphOne
    {
        return $this->morphOne(Meta::class, 'metable');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }
}
