<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\InventoryItem;

return new class extends Migration {
    public function up(): void {
        $books = [
            'النص الاساسى',
            'إنه يعمل - كيف ولماذا',
            'دليل العمل بالخطوات',
            'كتاب لليوم فقط',
            'الدليل التمهيدى',
            'روح تقاليدنا',
            'العيش ممتنعًا',
            'النص الاساسى (جيب)',
            'إنة يعمل - كيف ولماذا (جيب)',
            'دليل العمل بالخطوات (جيب)',
            'كتاب لليوم فقط (جيب)',
            'النص الاساسى (تذكاري)',
            'أنة يعمل - كيف ولماذا (تذكاري)',
            'دليل عمل الخطوات (تذكاري)',
            'كتاب لليوم فقط (تذكاري)',
        ];

        foreach ($books as $book) {
            InventoryItem::firstOrCreate([
                'name' => $book,
            ], [
                'selling_price' => 0.00,
                'store_quantity' => 0,
                'lit_quantity' => 0,
                'category' => 'Arabic Books',
            ]);
        }
    }

    public function down(): void {
        // Safe to leave empty
    }
};
