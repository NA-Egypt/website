<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_committees', function (Blueprint $table) {
            if (!Schema::hasColumn('service_committees', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('service_committees')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('service_committees', 'workgroup_type')) {
                $table->enum('workgroup_type', ['permanent', 'temporary'])
                    ->nullable()
                    ->after('parent_id');
            }

            if (!Schema::hasColumn('service_committees', 'status')) {
                $table->enum('status', ['active', 'completed', 'archived'])
                    ->default('active')
                    ->after('workgroup_type');
            }

            if (!Schema::hasColumn('service_committees', 'start_date')) {
                $table->date('start_date')
                    ->nullable()
                    ->after('status');
            }

            if (!Schema::hasColumn('service_committees', 'end_date')) {
                $table->date('end_date')
                    ->nullable()
                    ->after('start_date');
            }
        });

        // Ensure 'Workgroups' role exists
        $workgroupRole = Role::firstOrCreate(['name' => 'Workgroups', 'guard_name' => 'web']);

        // Update existing IT Workgroup (id: 8) to belong to Egypt Regional Service Committee (id: 83) as permanent workgroup
        $itWorkgroup = DB::table('service_committees')->where('id', 8)->first();
        $rsc = DB::table('service_committees')->where('id', 83)->first();

        if ($itWorkgroup && $rsc) {
            DB::table('service_committees')->where('id', 8)->update([
                'parent_id' => 83,
                'workgroup_type' => 'permanent',
                'status' => 'active',
            ]);
        }

        // Assign user 225 the 'Workgroups' role
        $user225 = User::find(225);
        if ($user225) {
            $user225->assignRole('Workgroups');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_committees', function (Blueprint $table) {
            if (Schema::hasColumn('service_committees', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }

            $columnsToDrop = [];
            foreach (['workgroup_type', 'status', 'start_date', 'end_date'] as $col) {
                if (Schema::hasColumn('service_committees', $col)) {
                    $columnsToDrop[] = $col;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
