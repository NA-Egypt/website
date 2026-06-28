<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\InventoryItem;

return new class extends Migration {
    public function up(): void {
        $books = [
            'Basic Text - Reg Size',
            'Basic Text - Pocket Size',
            'Basic Text - Gift Edition',
            'JFT - Reg Size',
            'JFT - Pocket Size',
            'JFT - Gift Edition',
            'How & Why - Reg Size',
            'How & Why  - Pocket Size',
            'Sponsorship',
            'Sponsorship - Gift Edition',
            'Step Working Guide',
            'Introductory Guide to NA',
            'Living Clean',
            'Guiding Principle',
            'Spiritual Principle A Day',
            'Miracle Happens Hard',
            'Commemorative Living Clean',
            'Guiding Principles Special Edition',
            'SPAD Special Edition',
            'JFT Journal',
            'The Basic Library',
        ];

        foreach ($books as $book) {
            InventoryItem::firstOrCreate([
                'name' => $book,
            ], [
                'selling_price' => 0.00,
                'store_quantity' => 0,
                'lit_quantity' => 0,
                'category' => 'English Books',
            ]);
        }
    }

    public function down(): void {
        // Safe to leave empty
    }
};
