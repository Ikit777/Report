<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DailyReportAttachment extends Model
{
    protected $fillable = ['daily_report_id', 'section', 'attachment_key', 'context', 'path'];

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }

    /**
     * Get the public URL for this attachment.
     * Ensures browser-accessible URL is generated (not internal hostname).
     */
    public function getPublicUrl(): string
    {
        $disk = config('filesystems.report_attachment_disk', 'public');
        
        // For local/public disk, use standard URL
        if ($disk !== 's3') {
            return Storage::disk($disk)->url($this->path);
        }
        
        // For S3/MinIO, ensure public URL
        $url = Storage::disk('s3')->url($this->path);
        
        // Replace internal/localhost URLs with public Railway domain
        $patterns = [
            '#^https?://minio\.railway\.internal:9000#i',
            '#^https?://localhost:9000#i',
            '#^http://minio-production-f981\.up\.railway\.app#i', // Force HTTPS
        ];
        
        $replacement = 'https://minio-production-f981.up.railway.app';
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                $url = preg_replace($pattern, $replacement, $url);
                break;
            }
        }
        
        return $url;
    }
}
