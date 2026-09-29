<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppSubscriber extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_subscribers';

    protected $fillable = [
        'jid',
        'phone',
        'name',
        'channel',
        'is_active',
        'is_dev_subscriber',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_dev_subscriber' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active subscribers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to channel.
     */
    public function scopeChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }
}
