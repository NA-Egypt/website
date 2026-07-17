<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{

    protected $fillable = ['ar_name', 'en_name', 'latitude', 'longitude'];
    public function neighborhoods(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Neighborhood::class);
    }
    
}
