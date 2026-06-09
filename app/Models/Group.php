<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    protected $fillable = [
        'ar_name',
        'en_name',
        'ar_gsr_name',
        'en_gsr_name',
        'user_id',
        'phone',
        'location',
        'ar_address',
        'en_address',
        'group_type',
        'service_body_id',
        'neighborhood_id',
        'capacity'
    ];
    public function serviceBody(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ServiceBody::class);
    }

    public function neighborhood(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function meetings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function transaction(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agendas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Agenda::class);
    }
}
