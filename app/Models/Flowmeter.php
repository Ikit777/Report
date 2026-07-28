<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flowmeter extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'unit',
        'jenis',
        'nomor_seri',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Site
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
