<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportItem extends Model
{
    protected $fillable = [
        'daily_report_id',
        'tank_id',
        'main_hole_variant',
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
    
    public function getMainHoleDisplayAttribute()
    {
        // Extract variant from keterangan if it starts with [variant]
        if ($this->keterangan && preg_match('/^\[(DEPAN|BELAKANG|\(DEPAN \+ BELAKANG\) \/ 2)\]/', $this->keterangan, $matches)) {
            return $matches[1];
        }
        
        // Check if this is one of multiple rows for same tank in same report
        if ($this->tank && $this->tank->main_hole === '(DEPAN + BELAKANG) / 2' && $this->dailyReport) {
            $sameTankItems = $this->dailyReport->items->where('tank_id', $this->tank_id)->values();
            if ($sameTankItems->count() === 3) {
                $index = $sameTankItems->search(fn($item) => $item->id === $this->id);
                if ($index === 0) return 'DEPAN';
                if ($index === 1) return 'BELAKANG';
                if ($index === 2) return '(DEPAN + BELAKANG) / 2';
            }
        }
        
        // Default: return tank's main_hole
        return $this->tank?->main_hole ?? '-';
    }
    
    public function getKeteranganDisplayAttribute()
    {
        // Remove [variant] prefix from keterangan for display
        if ($this->keterangan && preg_match('/^\[(DEPAN|BELAKANG|\(DEPAN \+ BELAKANG\) \/ 2)\]\s*(.*)$/', $this->keterangan, $matches)) {
            return $matches[2] ?: null;
        }
        return $this->keterangan;
    }

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }
}
