<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\InventoryItem;

return new class extends Migration {
    public function up(): void {
        $chips = [
            'Welcome / White',
            '30 Days / Orange',
            '60 Days / Green',
            '90 Days / Red',
            '6 Months / Blue',
            '9 Months / Yellow',
            '1 Year / Moonglow',
            '18 Months / Grey',
            'Multiple Years / Black',
        ];

        foreach ($chips as $chip) {
            InventoryItem::firstOrCreate([
                'name' => $chip,
            ], [
                'selling_price' => 0.00,
                'store_quantity' => 0,
                'lit_quantity' => 0,
                'category' => 'Chips',
            ]);
        }
    }

    public function down(): void {
        // Safe to leave empty
    }
};
