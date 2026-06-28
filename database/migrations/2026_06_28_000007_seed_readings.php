<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\InventoryItem;

return new class extends Migration {
    public function up(): void {
        $readings = [
            'مجموعة قراءات (عربي)',
            'مجموعة قراءات (انجليزي)',
        ];

        foreach ($readings as $reading) {
            InventoryItem::firstOrCreate([
                'name' => $reading,
            ], [
                'selling_price' => 0.00,
                'store_quantity' => 0,
                'lit_quantity' => 0,
                'category' => 'Readings',
            ]);
        }
    }

    public function down(): void {
        // Safe to leave empty
    }
};
