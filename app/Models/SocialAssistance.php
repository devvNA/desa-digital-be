<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class SocialAssistance extends Model
{
    use SoftDeletes, UUID;

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
