<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppConversation extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'jid',
        'phone',
        'name',
        'is_live_agent_mode',
        'live_agent_until',
        'last_assigned_user_id',
        'current_step',
        'step_data',
        'is_dev_test',
        'last_interaction_at',
    ];

    protected $casts = [
        'is_live_agent_mode' => 'boolean',
        'live_agent_until' => 'datetime',
        'is_dev_test' => 'boolean',
        'step_data' => 'array',
        'last_interaction_at' => 'datetime',
    ];

    /**
     * Get the messages for this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id');
    }

    /**
     * Get the last volunteer/agent who handled this conversation.
     */
    public function lastAssignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_assigned_user_id');
    }

    /**
     * Determine if Live Agent mode is actively suppressing the bot.
     */
    public function isLiveAgentActive(): bool
    {
        if (!$this->is_live_agent_mode) {
            return false;
        }

        if ($this->live_agent_until && $this->live_agent_until->isPast()) {
            $this->update([
                'is_live_agent_mode' => false,
                'live_agent_until' => null,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Activate Live Agent mode for a given duration in minutes.
     */
    public function enableLiveAgent(?int $minutes = null, ?int $userId = null): void
    {
        $duration = $minutes ?? config('whatsapp.live_agent_timeout_minutes', 30);
        $this->update([
            'is_live_agent_mode' => true,
            'live_agent_until' => Carbon::now()->addMinutes($duration),
            'last_assigned_user_id' => $userId ?? $this->last_assigned_user_id,
            'current_step' => null,
        ]);
    }

    /**
     * Deactivate Live Agent mode and restore the bot.
     */
    public function disableLiveAgent(): void
    {
        $this->update([
            'is_live_agent_mode' => false,
            'live_agent_until' => null,
            'current_step' => null,
            'step_data' => null,
        ]);
    }
}
