<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FamilyMember extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'head_of_family_id',
        'user_id',
        'profile_picture',
        'identity_number',
        'gender',
        'date_of_birth',
        'phone_number',
        'occupation',
        'marital_status',
        'relation'
    ];

    public function headOfFamily()
    {
        return $this->belongsTo(HeadOfFamily::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
