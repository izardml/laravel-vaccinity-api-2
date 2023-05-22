<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spot extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = [
        'regional_id',
    ];
    protected $appends = [
        'available_vaccines'
    ];

    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }

    public function getAvailableVaccinesAttribute()
    {
        $available_vaccines = [];
        foreach(Vaccine::all() as $vaccine) {
            $available_vaccines[$vaccine->name] = SpotVaccine::where([
                'spot_id' => $this->id,
                'vaccine_id' => $vaccine->id
            ])->first() ? true : false;
        }

        return $available_vaccines;
    }
}
