<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One article a super admin has picked to appear in a given "pick list"
 * slider/carousel — either one of the four homepage sections (Latest
 * Pieces, The Selection, Features, Journal — see
 * resources/views/frontend/partials/home/), or one of the two Features
 * page-specific sections (Highlighted, More from this edition — see
 * resources/views/frontend/partials/features/edition.blade.php). Ordered
 * within its section by sort_order.
 */
class HomeSectionArticle extends Model
{
    public const SECTION_LATEST_PIECES = 'latest_pieces';

    public const SECTION_THE_SELECTION = 'the_selection';

    public const SECTION_FEATURES = 'features';

    public const SECTION_JOURNAL = 'journal';

    // The Features page's own two sections — feature articles only, never
    // offered on Journals or on the Home Page Sections screen (which is
    // homepage-only; see App\Support\HomeSections::LABELS vs
    // ::FEATURES_PAGE_LABELS).
    public const SECTION_FEATURES_HIGHLIGHTED = 'features_highlighted';

    public const SECTION_FEATURES_EDITION = 'features_edition';

    /**
     * The four homepage sections managed on the Home Page Sections screen.
     */
    public const SECTIONS = [
        self::SECTION_LATEST_PIECES,
        self::SECTION_THE_SELECTION,
        self::SECTION_FEATURES,
        self::SECTION_JOURNAL,
    ];

    /**
     * The Features page's own two sections, toggled from the Features
     * list/edit admin pages instead.
     */
    public const FEATURES_PAGE_SECTIONS = [
        self::SECTION_FEATURES_HIGHLIGHTED,
        self::SECTION_FEATURES_EDITION,
    ];

    protected $fillable = [
        'section',
        'article_id',
        'sort_order',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function scopeForSection(Builder $query, string $section): Builder
    {
        return $query->where('section', $section)->orderBy('sort_order');
    }
}
