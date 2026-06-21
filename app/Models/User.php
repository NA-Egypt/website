<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'email',
        'password',
        'type',
        'service_body_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    public function group()
    {
        return $this->hasOne(Group::class);
    }

    public function serviceBody()
    {
        return $this->belongsTo(ServiceBody::class);
    }

    public function getServiceBodyAttribute()
    {
        if ($this->hasRole('gsr')) {
            $group = Group::where('user_id', $this->id)->first();
            return $group ? $group->serviceBody : null;
        }

        return $this->getRelationValue('serviceBody');
    }

    public function getServiceBodyIdAttribute($value)
    {
        if ($this->hasRole('gsr')) {
            $group = Group::where('user_id', $this->id)->first();
            return $group ? $group->service_body_id : $value;
        }
        return $value;
    }
}
