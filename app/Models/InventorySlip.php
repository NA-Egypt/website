<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InventorySlip extends Model
{
    protected $fillable = [
        'slip_number',
        'type',
        'status',
        'issued_by',
        'received_by',
        'received_at',
        'total_items_count',
        'total_value',
        'notes',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'total_value' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(InventorySlipItem::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Generate sequential slip number (e.g., TR-202608-0001, RT-202608-0001)
     */
    public static function generateSlipNumber(string $type): string
    {
        $prefix = ($type === 'return_to_store') ? 'RT' : 'TR';
        $yearMonth = Carbon::now()->format('Ym');
        $pattern = "{$prefix}-{$yearMonth}-%";

        $lastSlip = self::where('slip_number', 'like', $pattern)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSlip) {
            $parts = explode('-', $lastSlip->slip_number);
            $seq = intval(end($parts)) + 1;
        } else {
            $seq = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $yearMonth, $seq);
    }
}
