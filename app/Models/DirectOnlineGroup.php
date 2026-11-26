<?php

namespace App\Models;

use App\Traits\HasNormalizedSearchText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DirectOnlineGroup extends Model
{
    use HasNormalizedSearchText;
    use SoftDeletes;

    protected $table = 'direct_online_groups';

    protected $hidden = ['search_text'];

    protected function searchableTextFields(): array
    {
        return ['ar_name', 'en_name'];
    }

    protected $fillable = [
        'ar_name',
        'en_name',
        'ar_gsr_name',
        'en_gsr_name',
        'phone',
        'location',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'direct_online_group_id');
    }
}
