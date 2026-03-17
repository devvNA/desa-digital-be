<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SocialAssistance extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'thumbnail',
        'name',
        'category',
        'amount',
        'provider',
        'description',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function socialAssistanceRecipient()
    {
        return $this->hasMany(SocialAssistanceRecipient::class);
    }
}
