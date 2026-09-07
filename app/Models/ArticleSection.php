<?php

namespace App\Models;

use Database\Factories\ArticleSectionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ArticleSection extends Model
{
    /** @use HasFactory<ArticleSectionFactory> */
    use HasFactory;

    // A section carries at most one of these — never both at once (see the
    // Content-tab media-type toggle on the admin form).
    public const MEDIA_IMAGE = 'image';

    public const MEDIA_VIDEO = 'video';

    public const IMAGE_LEFT = 'left';

    public const IMAGE_RIGHT = 'right';

    public const IMAGE_CENTER = 'center';

    // 'beside' always means "on the side opposite the image" — there's no
    // independent left/right choice for content, which is what used to let
    // image_position and content_position both be set to 'left' at once.
    public const CONTENT_BESIDE = 'beside';

    public const CONTENT_STANDALONE = 'standalone';

    // What appears on the side opposite the video: the section's own
    // `content` field ('text'), a dedicated upload ('image'), or nothing.
    public const VIDEO_COMPANION_TEXT = 'text';

    public const VIDEO_COMPANION_IMAGE = 'image';

    public const VIDEO_COMPANION_NONE = 'none';

    protected $fillable = [
        'article_id',
        'sort_order',
        'title',
        'media_type',
        'content',
        'image_position',
        'content_position',
        'youtube_url',
        'youtube_position',
        'video_companion_type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Only active sections should render on the frontend — this scopes to
     * that; the admin list/edit views intentionally show every section
     * regardless of this flag.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * The section's single positioned image (media_type = 'image').
     */
    public function image(): MorphOne
    {
        return $this->morphOne(MediaFile::class, 'mediable')->where('role', 'section-image');
    }

    /**
     * The section's additional image gallery — independent of media_type,
     * available alongside either an image or a video.
     */
    public function galleryImages(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'mediable')->where('role', 'gallery')->orderBy('sort_order');
    }

    /**
     * The image shown on the side opposite the YouTube video, when
     * video_companion_type is 'image'.
     */
    public function videoCompanionImage(): MorphOne
    {
        return $this->morphOne(MediaFile::class, 'mediable')->where('role', 'video-companion-image');
    }
}
