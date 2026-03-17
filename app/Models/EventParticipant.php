<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventParticipant extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'event_id',
        'head_of_family_id',
        'quantity',
        'total_price',
        'payment_status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function headOfFamily(): BelongsTo
    {
        return $this->belongsTo(HeadOfFamily::class);
    }
}
