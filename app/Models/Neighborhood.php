<?php

namespace App\Models;

use App\Traits\HasNormalizedSearchText;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    use HasNormalizedSearchText;

    protected $hidden = ['search_text'];

    protected function searchableTextFields(): array
    {
        return ['ar_name', 'en_name'];
    }

    protected $fillable = ['en_name', 'ar_name', 'city_id', 'latitude', 'longitude'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function meetings()
    {
        return $this->hasManyThrough(Meeting::class, Group::class);
    }
}
