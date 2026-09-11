<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCommittee extends Model
{
    protected $fillable = [
        'parent_id',
        'workgroup_type',
        'status',
        'start_date',
        'end_date',
        'ar_name',
        'en_name',
        'chairman_name',
        'chairman_phone',
        'email',
        'location',
        'ar_address',
        'en_address',
        'notes',
        'user_id',
        'logo',
        'default_footer'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function parent()
    {
        return $this->belongsTo(ServiceCommittee::class, 'parent_id');
    }

    public function workgroups()
    {
        return $this->hasMany(ServiceCommittee::class, 'parent_id');
    }

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

    public function scopeCommitteesOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeWorkgroupsOnly($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isWorkgroup(): bool
    {
        return !is_null($this->parent_id);
    }

    public function isCommittee(): bool
    {
        return is_null($this->parent_id);
    }
}
