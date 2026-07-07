<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectOnlineGroup extends Model
{
    protected $table = 'direct_online_groups';

    protected $fillable = [
        'ar_name',
        'en_name',
        'ar_gsr_name',
        'en_gsr_name',
        'phone',
        'location',
        'user_id'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meetings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Meeting::class, 'direct_online_group_id');
    }
}
