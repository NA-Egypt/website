<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['name', 'description', 'selling_price', 'store_quantity', 'lit_quantity', 'category'];

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

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
