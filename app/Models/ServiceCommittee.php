<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCommittee extends Model
{
    protected $fillable = ['ar_name', 'en_name', 'chairman_name', 'chairman_phone', 'email', 'location', 'ar_address', 'en_address'];

    public function meetings()
    {
        return $this->hasMany(ScMeeting::class);
    }

}
