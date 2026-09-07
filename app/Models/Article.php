<?php

namespace App\Models;

use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    // Deliberately plural ('features'/'journals') — unlike Category::TYPE_*,
    // this matches the admin route names, permission suffixes, and
    // config/media.php keys used throughout the Features/Journals admin
    // module (see AbstractArticleController::categoryType() for the mapping
    // back to Category's singular type when scoping the category dropdown).
    public const TYPE_FEATURE = 'features';

    public const TYPE_JOURNAL = 'journals';

    // The singleton "Our Story" article — content-only (Content tab/
    // sections alone; no Essentials, FAQs, or Page Sections in the admin),
    // filed under Category::TYPE_OUR_STORY's single hidden category. Value
    // matches the admin route name/permission suffix/config/media.php key,
    // same convention as the two above.
    public const TYPE_OUR_STORY = 'our-story';

    // Not yet submitted for review — the creator is still working on it.
    // An article can only ever reach here before its first approval; once
    // approved_at is set (it has been active at least once), it can no
    // longer go back to draft — only toggled active/inactive from there.
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'uuid',
        'category_id',
        'type',
        'title',
        'slug',
        'introduction',
        'editors_note',
        'authors_note',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Article $article): void {
            $article->uuid ??= (string) Str::uuid();
        });

        // Every content page (Feature/Journal article) gets its own SEO
        // meta row the moment it's created, ready to edit straight away —
        // Our Story is excluded since it's managed as one of the fixed
        // static pages instead (see Meta::STATIC_PAGES).
        static::created(function (Article $article): void {
            if ($article->type !== self::TYPE_OUR_STORY) {
                $article->meta()->create([]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The user who created this article.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The super admin who approved this article, if any.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return HasMany<ArticleSection, $this>
     */
    public function sections(): HasMany
    {
        return $this->hasMany(ArticleSection::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ArticleFaq, $this>
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(ArticleFaq::class)->orderBy('sort_order');
    }

    /**
     * The article's featured image.
     */
    public function featuredImage(): MorphOne
    {
        return $this->morphOne(MediaFile::class, 'mediable')->where('role', 'featured');
    }

    /**
     * This content page's own SEO meta data (title/description/keywords).
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
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Whether the "Save as Draft" option is still available — only before
     * the article has ever been approved/active.
     */
    public function canBeDrafted(): bool
    {
        return $this->approved_at === null;
    }
}
