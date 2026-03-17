<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentApplicant extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'development_id',
        'user_id',
        'status',
    ];

    public function development(): BelongsTo
    {
        return $this->belongsTo(Development::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
