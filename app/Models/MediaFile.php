<?php

namespace App\Models;

use Database\Factories\MediaFileFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaFile extends Model
{
    /** @use HasFactory<MediaFileFactory> */
    use HasFactory;

    public const TYPE_GALLERY = 'gallery';

    public const TYPE_STUDIO = 'studio';

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'uuid',
        'type',
        'file_name',
        'thumbnail_path',
        'large_path',
        'original_path',
        'title',
        'caption',
        'status',
        'uploaded_by',
        'approved_by',
        'approved_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (MediaFile $mediaFile): void {
            $mediaFile->uuid ??= (string) Str::uuid();
        });
    }

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    /**
     * The user who uploaded this media file.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * The super admin who approved this media file, if any.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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
     * @return Attribute<?string, never>
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->thumbnail_path
                ? Storage::disk(config("media.{$this->type}.disk"))->url($this->thumbnail_path)
                : null,
        );
    }

    /**
     * @return Attribute<?string, never>
     */
    protected function largeUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->large_path
                ? Storage::disk(config("media.{$this->type}.disk"))->url($this->large_path)
                : null,
        );
    }

    /**
     * @return Attribute<?string, never>
     */
    protected function originalUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->original_path
                ? Storage::disk(config("media.{$this->type}.disk"))->url($this->original_path)
                : null,
        );
    }
}
