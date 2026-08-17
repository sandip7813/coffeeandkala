<?php

namespace App\Models;

use Database\Factories\QuoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Quote extends Model
{
    /** @use HasFactory<QuoteFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'text',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quote $quote): void {
            $quote->uuid ??= (string) Str::uuid();
        });
    }

    /**
     * The admin who created this quote.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Dates this quote has been scheduled for.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(QuoteSchedule::class);
    }

    /**
     * The next date (today or later) this quote is scheduled to be shown on, if any.
     */
    public function upcomingSchedule(): HasOne
    {
        return $this->hasOne(QuoteSchedule::class)->ofMany(
            ['date' => 'min'],
            fn ($query) => $query->whereDate('date', '>=', Carbon::today())
        );
    }
}
