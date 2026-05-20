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
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed for rollback as this is a data repair migration.
    }
};
