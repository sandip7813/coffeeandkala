<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * SEO meta data (title/description/keywords) for a frontend page — either
 * one of the site's fixed static pages (page_key) or a dynamic record's own
 * page (the polymorphic metable_* columns: a Category for a Feature/Journal
 * category page, or an Article for a Features/Journals content page).
 */
class Meta extends Model
{
    /**
     * The site's fixed, singleton frontend pages — managed together on the
     * admin "Meta Data" screen (Site Manager). Category pages and Article
     * content pages instead get their own meta via metable, edited on
     * their own admin edit screen.
     *
     * @var array<string, string>
     */
    public const STATIC_PAGES = [
        'home' => 'Home Page',
        'our-story' => 'Our Story',
        'features' => 'Features',
        'journal' => 'Journal',
        'studio' => 'Studio',
        'gallery' => 'Gallery',
        'poetry' => 'Poetry',
    ];

    protected $fillable = [
        'page_key',
        'metable_type',
        'metable_id',
        'title',
        'description',
        'keywords',
    ];

    public function metable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The meta row for one of the fixed static pages, created on first
     * access if it doesn't exist yet.
     */
    public static function forPage(string $key): self
    {
        return self::firstOrCreate(['page_key' => $key]);
    }
}
