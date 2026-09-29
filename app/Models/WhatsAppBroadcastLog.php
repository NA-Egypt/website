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
        'title',
        'total_recipients',
        'successful_count',
        'failed_count',
        'is_dev_broadcast',
        'dispatched_by',
        'completed_at',
    ];

    protected $casts = [
        'is_dev_broadcast' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user who dispatched this broadcast (if manual).
     */
    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }
}
