<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class SocialAssistanceRecipient extends Model
{
    use SoftDeletes, UUID;

    protected $fillable = [ 
        'social_assistance_id',
        'head_of_family_id',
        'bank',
        'amount',     
        'reason',
        'account_number',
        'proof',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function socialAssistance()
    {
        return $this->belongsTo(SocialAssistance::class);
    }

    public function headOfFamily()
    {
        return $this->belongsTo(HeadOfFamily::class);
    }
}
