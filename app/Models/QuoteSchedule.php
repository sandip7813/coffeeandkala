<?php

namespace App\Models;

use Database\Factories\QuoteScheduleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteSchedule extends Model
{
    /** @use HasFactory<QuoteScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'quote_id',
        'assigned_by',
        'is_auto_assigned',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_auto_assigned' => 'boolean',
        ];
    }

    /**
     * The quote assigned to this date.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * The admin who assigned the quote to this date, if assigned manually.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
