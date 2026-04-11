<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'thumbnail',
        'name',
        'description',
        'price',
        'date',
        'time',
        'is_active',
    ];

    protected function time(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->format('H:i') : null,
        );
    }

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function scopeSearch($query, ?string $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    public function eventParticipants()
    {
        return $this->hasMany(EventParticipant::class);
    }
}
