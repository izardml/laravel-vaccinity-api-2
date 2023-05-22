<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Society extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = [
        'id',
        'id_card_number',
        'password',
        'regional_id',
        'login_tokens'
    ];

    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }
}
