<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('literature_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('service_body_id')->nullable()->change();
            if (!Schema::hasColumn('literature_requests', 'service_committee_id')) {
                $table->foreignId('service_committee_id')->nullable()->after('group_id')->constrained('service_committees')->nullOnDelete();
            }
        });

        Schema::table('inventory_slips', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_slips', 'service_committee_id')) {
                $table->foreignId('service_committee_id')->nullable()->after('type')->constrained('service_committees')->nullOnDelete();
            }
            if (!Schema::hasColumn('inventory_slips', 'literature_request_id')) {
                $table->foreignId('literature_request_id')->nullable()->after('service_committee_id')->constrained('literature_requests')->nullOnDelete();
            }
        });
    }

    public function down(): void {
        Schema::table('inventory_slips', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_slips', 'literature_request_id')) {
                $table->dropConstrainedForeignId('literature_request_id');
            }
            if (Schema::hasColumn('inventory_slips', 'service_committee_id')) {
                $table->dropConstrainedForeignId('service_committee_id');
            }
        });

        Schema::table('literature_requests', function (Blueprint $table) {
            if (Schema::hasColumn('literature_requests', 'service_committee_id')) {
                $table->dropConstrainedForeignId('service_committee_id');
            }
        });
    }
};
