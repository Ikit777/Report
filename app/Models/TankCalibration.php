<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TankCalibration extends Model
{
    protected $fillable = [
        'tank_id',
        'sounding_cm',
        'sounding_mm',
        'volume_liters',
    ];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }
}
