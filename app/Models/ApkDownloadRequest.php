<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApkDownloadRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'ip_address',
        'user_agent',
        'expires_at',
        'download_count',
        'last_downloaded_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
        'download_count' => 'integer',
    ];

    /**
     * Determine if the download request is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Determine if the download request is valid for downloading.
     */
    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    /**
     * Record a download event.
     */
    public function recordDownload(): void
    {
        $this->increment('download_count');
        $this->update(['last_downloaded_at' => now()]);
    }
}
