<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportTransfer extends Model
{
    protected $fillable = [
        'daily_report_id',
        'dari_tangki',
        'ke_tangki',
        'spm_awal',
        'spm_akhir',
        'spm_hasil',
        'spm_liter',
        'ft_awal',
        'ft_akhir',
        'ft_hasil',
        'ft_liter',
        'fm_awal',
        'fm_akhir',
        'fm_jumlah',
        'jam_mulai',
        'jam_selesai',
        'lama_transfer',
    ];

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }
}
