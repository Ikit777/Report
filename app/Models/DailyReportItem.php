<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportItem extends Model
{
    protected $fillable = [
        'daily_report_id',
        'tank_id',
        'sounding_pagi',
        'liter_pagi',
        'jam_pagi',
        'petugas_pagi',
        'sounding_sore',
        'liter_sore',
        'jam_sore',
        'petugas_sore',
        'fm_pagi',
        'fm_sore',
        'fm_pakai',
        'keterangan',
    ];

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }
}
