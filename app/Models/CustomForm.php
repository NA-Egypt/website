<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomForm extends Model
{
    protected $fillable = [
        'title',
        'type',
        'status',
        'slug',
        'views',
        'user_id',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($form) {
            if (empty($form->slug)) {
                $form->slug = Str::random(12);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fields()
    {
        return $this->hasMany(CustomFormField::class, 'custom_form_id')->orderBy('sort_order');
    }

    public function submissions()
    {
        return $this->hasMany(CustomFormSubmission::class, 'custom_form_id');
    }

    public function getConversionRateAttribute()
    {
        if ($this->views <= 0) {
            return 0;
        }
        return round(($this->submissions()->count() / $this->views) * 100, 1);
    }
}
