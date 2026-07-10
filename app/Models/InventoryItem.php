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
        $isEnglishCategory = in_array($this->category, ['English Books', 'English IP']);
        
        if ($isEnglishCategory) {
            return $this->name_en ?: $this->name;
        }
        
        return $this->name ?: $this->name_en;
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
