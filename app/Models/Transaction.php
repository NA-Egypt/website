<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class Transaction extends Model
{
    use Prunable;

    protected $fillable = [
        'model',
        'operation',
        'details',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'user_id'
    ];

    protected $casts = [
        'details' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the prunable model query.
     */
    public function pruning(): \Illuminate\Database\Eloquent\Builder
    {
        return static::where('created_at', '<=', now()->subDays(90));
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Optional: Link the transaction to a user
    }

    public function groups() {
        return $this->belongsTo(Group::class);
    }
}
