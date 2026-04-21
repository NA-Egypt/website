<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCommittee extends Model
{
    protected $fillable = ['ar_name', 'en_name', 'chairman_name', 'chairman_phone', 'email', 'location', 'ar_address', 'en_address', 'notes'];

    public function meetings()
    {
        return $this->hasMany(ScMeeting::class);
    }

    public function reports()
    {
        return $this->hasMany(CommitteeReport::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
