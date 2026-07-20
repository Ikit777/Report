<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportFlowmeter extends Model
{
    protected $fillable = [
        'daily_report_id',
        'unit',
        'jenis_flowmeter',
        'nomor_seri',
        'awal_pagi',
        'akhir_sore',
        'jumlah_pakai',
    ];

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }
}
