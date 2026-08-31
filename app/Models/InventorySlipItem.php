<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventorySlipItem extends Model
{
    protected $fillable = [
        'inventory_slip_id',
        'inventory_item_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function slip()
    {
        return $this->belongsTo(InventorySlip::class, 'inventory_slip_id');
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
