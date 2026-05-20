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
        // 1. Add review_notes to committee_reports
        if (!Schema::hasColumn('committee_reports', 'review_notes')) {
            Schema::table('committee_reports', function (Blueprint $table) {
                $table->text('review_notes')->nullable()->after('status');
            });
        }

        // 2. Fix service_committees numeric emails and user_id mapping
        $committees = DB::table('service_committees')->get();
        foreach ($committees as $committee) {
            if (isset($committee->email) && is_numeric($committee->email)) {
                $user = DB::table('users')->where('id', (int)$committee->email)->first();
                if ($user) {
                    DB::table('service_committees')
                        ->where('id', $committee->id)
                        ->update([
                            'user_id' => $user->id,
                            'email' => $user->email,
                        ]);
                }
            } else if (isset($committee->email)) {
                $user = DB::table('users')->where('email', $committee->email)->first();
                if ($user) {
                    DB::table('service_committees')
                        ->where('id', $committee->id)
                        ->update([
                            'user_id' => $user->id,
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('committee_reports', 'review_notes')) {
            Schema::table('committee_reports', function (Blueprint $table) {
                $table->dropColumn('review_notes');
            });
        }
    }
};
