<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\InventoryItem;

return new class extends Migration {
    public function up(): void {
        $ips = [
            'للعضو الجديد',
            'هل انا مدمن',
            'مرحبا بك فى زمالة م.م.',
            'لليوم فقط',
            'التوجية',
            'نظرة أخرى',
            'التعافى والانتكاس',
            'تقبل الذات',
            'مثلث الهاجس الذاتي',
            'مقدمة لاجتماعات زمالة م.م',
            'من - ماذا - كيف ولماذا',
            'تجربة مدمن مع التقبل والإيمان والالتزام',
            'البقاء ممتنعا في الخارج',
            'رسالة لمن هم بالمصحات',
            'التواصل الاجتماعى',
            'الخدم الموثوق بهم',
            'الامور المالية',
            'تمويل الخدمات',
            'السلوك المعيق',
            'المجموعة',
            'معايشة البرنامج',
            'المنعزل - البقاء ممتنعاً في الخارج',
            'من المدمنين الشباب إلى المدمنين الشباب',
            'للآباء أو أولياء أمور المدمنين الشباب',
        ];

        foreach ($ips as $ip) {
            InventoryItem::firstOrCreate([
                'name' => $ip,
            ], [
                'selling_price' => 0.00,
                'store_quantity' => 0,
                'lit_quantity' => 0,
                'category' => 'Arabic IP',
            ]);
        }
    }

    public function down(): void {
        // Safe to leave empty
    }
};
