<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    public const TYPE_FEATURE = 'feature';

    public const TYPE_JOURNAL = 'journal';

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
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'status' => 'boolean',
        ];
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
