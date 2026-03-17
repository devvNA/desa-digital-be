<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'thumbnail',
        'name',
        'about',
        'headman',
        'people',
        'agricultural_area',
        'total_area',
    ];

    protected $casts = [
        'agricultural_area' => 'decimal:2',
        'total_area' => 'decimal:2',
    ];

    public function profileImages()
    {
        return $this->hasMany(ProfileImage::class);
    }
}
