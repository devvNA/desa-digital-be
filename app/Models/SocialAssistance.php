<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAssistance extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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

    public function scopeSearch($query, ?string $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('category', 'like', "%{$search}%")
            ->orWhere('provider', 'like', "%{$search}%")
            ->orWhere('amount', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    public function socialAssistanceRecipient()
    {
        return $this->hasMany(SocialAssistanceRecipient::class);
    }
}
