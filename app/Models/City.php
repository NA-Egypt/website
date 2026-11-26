<?php

namespace App\Models;

use App\Traits\HasNormalizedSearchText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasNormalizedSearchText;

    protected $hidden = ['search_text'];

    protected function searchableTextFields(): array
    {
        return ['ar_name', 'en_name'];
    }

    protected $fillable = ['ar_name', 'en_name', 'latitude', 'longitude'];

    public function neighborhoods(): HasMany
    {
        return $this->hasMany(Neighborhood::class);
    }
}
