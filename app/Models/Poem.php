<?php

namespace App\Models;

use Database\Factories\PoemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Poem extends Model
{
    /** @use HasFactory<PoemFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'body',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Poem $poem): void {
            $poem->uuid ??= (string) Str::uuid();
        });

        // Every poem gets its own SEO meta row the moment it's created, same
        // as Feature/Journal articles (see App\Models\Article).
        static::created(function (Poem $poem): void {
            $poem->meta()->create([]);
        });
    }

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    /**
     * The user who created this poem.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The super admin who approved this poem, if any.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * The poem's featured image.
     */
    public function featuredImage(): MorphOne
    {
        return $this->morphOne(MediaFile::class, 'mediable')->where('role', 'featured');
    }

    /**
     * This poem's own SEO meta data (title/description/keywords).
     */
    public function meta(): MorphOne
    {
        return $this->morphOne(Meta::class, 'metable');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
