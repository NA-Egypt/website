<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppBroadcastLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_broadcast_logs';

    protected $fillable = [
        'channel',
        'device_id',
        'title',
        'total_recipients',
        'successful_count',
        'failed_count',
        'status',
        'anti_ban_profile',
        'is_dev_broadcast',
        'dispatched_by',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'is_dev_broadcast' => 'boolean',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user who dispatched this broadcast (if manual).
     */
    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }
}
