<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class FamilyMember extends Model
{
    use SoftDeletes, UUID;

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
