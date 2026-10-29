<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'description',
        'selling_price',
        'store_quantity',
        'lit_quantity',
        'damaged_quantity',
        'category',
    ];

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

    public function damagedTransactions()
    {
        return $this->hasMany(InventoryTransaction::class)
            ->where('type', 'damaged')
            ->orderBy('created_at', 'desc');
    }

    public function lastActiveReceiveTransaction()
    {
        return $this->hasOne(InventoryTransaction::class)
            ->where('type', 'receive')
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->latestOfMany('id');
    }
}
