<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFormField extends Model
{
    protected $fillable = [
        'custom_form_id',
        'label',
        'type',
        'required',
        'options',
        'sort_order'
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(CustomForm::class, 'custom_form_id');
    }
}
