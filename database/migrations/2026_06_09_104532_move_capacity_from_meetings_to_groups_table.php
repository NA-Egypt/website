<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            if (!Schema::hasColumn('groups', 'capacity')) {
                $table->integer('capacity')->nullable()->after('group_type');
            }
        });

        // Migrate data
        $groups = DB::table('groups')->get();
        foreach ($groups as $group) {
            $maxCapacity = DB::table('meetings')
                ->where('group_id', $group->id)
                ->whereNotNull('capacity')
                ->where('capacity', '<>', '')
                ->get()
                ->map(function ($meeting) {
                    return (int) $meeting->capacity;
                })
                ->max();

            if ($maxCapacity !== null && $maxCapacity > 0) {
                DB::table('groups')->where('id', $group->id)->update(['capacity' => $maxCapacity]);
            }
        }

        Schema::table('meetings', function (Blueprint $table) {
            if (Schema::hasColumn('meetings', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            if (!Schema::hasColumn('meetings', 'capacity')) {
                $table->string('capacity')->nullable();
            }
        });

        // Copy capacity back from groups to meetings
        $groups = DB::table('groups')->get();
        foreach ($groups as $group) {
            if ($group->capacity !== null) {
                DB::table('meetings')
                    ->where('group_id', $group->id)
                    ->update(['capacity' => $group->capacity]);
            }
        }

        Schema::table('groups', function (Blueprint $table) {
            if (Schema::hasColumn('groups', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });
    }
};

