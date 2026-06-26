<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['name', 'description', 'selling_price', 'store_quantity', 'lit_quantity'];

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
