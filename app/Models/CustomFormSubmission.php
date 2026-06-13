<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFormSubmission extends Model
{
    protected $fillable = [
        'custom_form_id',
        'user_id',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(CustomForm::class, 'custom_form_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
