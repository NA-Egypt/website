<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StocktakingItem extends Model
{
    protected $fillable = [
        'stocktaking_session_id',
        'inventory_item_id',
        'system_store_qty',
        'system_lit_qty',
        'counted_store_qty',
        'counted_lit_qty',
        'store_variance',
        'lit_variance',
        'unit_price',
        'variance_value',
    ];

    public function session()
    {
        return $this->belongsTo(StocktakingSession::class, 'stocktaking_session_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function calculateVariances(): void
    {
        $countedStore = $this->counted_store_qty ?? $this->system_store_qty;
        $countedLit = $this->counted_lit_qty ?? $this->system_lit_qty;

        $this->store_variance = $countedStore - $this->system_store_qty;
        $this->lit_variance = $countedLit - $this->system_lit_qty;

        $totalVarianceQty = $this->store_variance + $this->lit_variance;
        $this->variance_value = $totalVarianceQty * $this->unit_price;
    }
}
