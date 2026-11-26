<?php

namespace App\Models;

use App\Traits\HasNormalizedSearchText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasNormalizedSearchText;
    use SoftDeletes;

    protected $hidden = ['search_text'];

    protected function searchableTextFields(): array
    {
        return ['ar_name', 'en_name', 'ar_address', 'en_address'];
    }

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
        'capacity',
    ];

    public function serviceBody(): BelongsTo
    {
        return $this->belongsTo(ServiceBody::class);
    }

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class);
    }

    public function literatureRequests(): HasMany
    {
        return $this->hasMany(LiteratureRequest::class);
    }

    public function scopeInPersonOnly($query)
    {
        return $query->whereNotIn('group_type', ['online', 'اونلاين', 'اون لاين']);
    }
}
