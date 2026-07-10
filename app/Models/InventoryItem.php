<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['name', 'name_en', 'description', 'selling_price', 'store_quantity', 'lit_quantity', 'category'];

    public const CATEGORIES = [
        'Arabic Books',
        'Arabic IP',
        'English Books',
        'English IP',
        'Chips',
        'Coins',
        'Medallions',
        'Readings',
        'Others'
    ];

    public function getStoreDisplayNameAttribute()
    {
        return $this->name_en ? "{$this->name_en} / {$this->name}" : $this->name;
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
