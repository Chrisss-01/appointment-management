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
        Schema::table('appointments', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('cancelled_at');
        });

        // Backfill completed_at with updated_at for existing completed appointments
        DB::table('appointments')
            ->where('status', 'completed')
            ->whereNull('completed_at')
            ->update([
                'completed_at' => DB::raw('updated_at')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};
