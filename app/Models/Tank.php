<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tank extends Model
{
    protected $fillable = [
        'code',
        'main_hole',
        'capacity',
        'is_active',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(DailyReportItem::class);
    }

    public function calibrations(): HasMany
    {
        return $this->hasMany(TankCalibration::class);
    }
}
