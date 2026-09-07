<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One Gallery/Studio image a super admin has picked to appear in the
 * homepage's own Gallery or Studio carousel (see
 * resources/views/frontend/partials/home/gallery-visual-storytelling.blade.php
 * and visual-poetry.blade.php). Ordered within its section by sort_order —
 * the media equivalent of HomeSectionArticle.
 */
class HomeSectionMedia extends Model
{
    public const SECTION_GALLERY = 'gallery';

    public const SECTION_STUDIO = 'studio';

    public const SECTIONS = [
        self::SECTION_GALLERY,
        self::SECTION_STUDIO,
    ];

    protected $fillable = [
        'section',
        'media_id',
        'sort_order',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'media_id');
    }

    public function scopeForSection(Builder $query, string $section): Builder
    {
        return $query->where('section', $section)->orderBy('sort_order');
    }
}
