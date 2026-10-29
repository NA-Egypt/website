<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_items', 'damaged_quantity')) {
                $table->integer('damaged_quantity')->default(0)->after('lit_quantity');
            }
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_transactions', 'source_location')) {
                $table->string('source_location')->nullable()->after('type');
            }
            if (!Schema::hasColumn('inventory_transactions', 'status')) {
                $table->string('status')->default('active')->after('notes');
            }
            if (!Schema::hasColumn('inventory_transactions', 'reference_transaction_id')) {
                $table->unsignedBigInteger('reference_transaction_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('inventory_transactions', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable()->after('reference_transaction_id');
            }
            if (!Schema::hasColumn('inventory_transactions', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $columns = array_filter(
                ['source_location', 'status', 'reference_transaction_id', 'cancelled_by', 'cancelled_at'],
                fn ($col) => Schema::hasColumn('inventory_transactions', $col)
            );
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_items', 'damaged_quantity')) {
                $table->dropColumn('damaged_quantity');
            }
        });
    }
};

