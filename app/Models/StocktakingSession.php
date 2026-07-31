<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StocktakingSession extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'notes',
        'started_at',
        'completed_at',
        'adjusted_at',
        'adjusted_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'adjusted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function adjustedByUser()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    public function items()
    {
        return $this->hasMany(StocktakingItem::class);
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isAdjusted(): bool
    {
        return $this->status === 'adjusted';
    }

    public static function getActiveSession()
    {
        return static::where('status', 'in_progress')->first();
    }
}
