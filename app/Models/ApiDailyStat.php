<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiDailyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'platform',
        'total_requests',
        'successful_requests',
        'client_error_requests',
        'server_error_requests',
        'avg_response_time_ms',
        'unique_ips',
        'unique_users',
        'top_endpoints_json',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'total_requests' => 'integer',
        'successful_requests' => 'integer',
        'client_error_requests' => 'integer',
        'server_error_requests' => 'integer',
        'avg_response_time_ms' => 'float',
        'unique_ips' => 'integer',
        'unique_users' => 'integer',
        'top_endpoints_json' => 'array',
    ];

    /**
     * Scope for platform filtering.
     */
    public function scopeForPlatform(Builder $query, ?string $platform): Builder
    {
        if ($platform && $platform !== 'all') {
            return $query->where('platform', $platform);
        }

        return $query;
    }

    /**
     * Scope for date range.
     */
    public function scopeForDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
