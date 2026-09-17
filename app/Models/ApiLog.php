<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiLog extends Model
{
    use HasFactory, MassPrunable;

    public $timestamps = false;

    protected $fillable = [
        'method',
        'endpoint',
        'route_name',
        'status_code',
        'response_time_ms',
        'platform',
        'app_version',
        'device_id',
        'ip_address',
        'user_id',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'response_time_ms' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the prunable model query (prunes records older than 30 days).
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(30));
    }

    /**
     * User associated with the API call.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering by platform.
     */
    public function scopeForPlatform(Builder $query, ?string $platform): Builder
    {
        if ($platform && $platform !== 'all') {
            return $query->where('platform', $platform);
        }

        return $query;
    }

    /**
     * Scope for filtering by status range (success, client_error, server_error).
     */
    public function scopeForStatus(Builder $query, ?string $status): Builder
    {
        if ($status === 'success') {
            return $query->whereBetween('status_code', [200, 299]);
        }
        if ($status === 'client_error') {
            return $query->whereBetween('status_code', [400, 499]);
        }
        if ($status === 'server_error') {
            return $query->whereBetween('status_code', [500, 599]);
        }
        if ($status === 'errors') {
            return $query->where('status_code', '>=', 400);
        }

        return $query;
    }

    /**
     * Badge CSS class for HTTP status code.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->status_code >= 200 && $this->status_code < 300) {
            return 'bg-success text-white';
        }
        if ($this->status_code >= 300 && $this->status_code < 400) {
            return 'bg-info text-dark';
        }
        if ($this->status_code >= 400 && $this->status_code < 500) {
            return 'bg-warning text-dark';
        }
        return 'bg-danger text-white';
    }

    /**
     * Badge CSS class for HTTP method.
     */
    public function getMethodBadgeClassAttribute(): string
    {
        return match (strtoupper($this->method)) {
            'GET' => 'badge-soft-info text-info border border-info-subtle',
            'POST' => 'badge-soft-success text-success border border-success-subtle',
            'PUT', 'PATCH' => 'badge-soft-warning text-warning border border-warning-subtle',
            'DELETE' => 'badge-soft-danger text-danger border border-danger-subtle',
            default => 'badge-soft-secondary text-secondary border border-secondary-subtle',
        };
    }

    /**
     * Bootstrap icon for the platform.
     */
    public function getPlatformIconAttribute(): string
    {
        return match (strtolower($this->platform)) {
            'ios' => 'bi-apple',
            'android' => 'bi-android2',
            'web' => 'bi-globe2',
            default => 'bi-hdd-network',
        };
    }
}
