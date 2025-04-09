<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['model', 'operation', 'details', 'user_id'];

    protected $casts = [
        'details' => 'array', // Automatically decode JSON details
    ];

    public function user()
    {
        return $this->belongsTo(User::class); // Optional: Link the transaction to a user
    }

    public function groups() {
        return $this->belongsTo(Group::class);
    }
}
