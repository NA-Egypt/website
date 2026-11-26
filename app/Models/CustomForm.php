<?php

namespace App\Models;

use App\Exceptions\UserFacingException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CustomForm extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'status',
        'slug',
        'views',
        'user_id',
        'settings',
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

        static::deleting(function (CustomForm $form) {
            if (! $form->isForceDeleting() && ! str_contains($form->slug, '__deleted_')) {
                $form->slug = $form->slug.'__deleted_'.$form->id;
                $form->saveQuietly();
            }
        });

        static::restoring(function (CustomForm $form) {
            $suffix = '__deleted_'.$form->id;
            if (str_ends_with($form->slug, $suffix)) {
                $originalSlug = substr($form->slug, 0, -strlen($suffix));
                if (static::where('slug', $originalSlug)->whereNull('deleted_at')->exists()) {
                    throw new UserFacingException(__('messages.flash.undo.slug_taken'), 'slug_taken', 422);
                }
                $form->slug = $originalSlug;
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

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'archived');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }
}
