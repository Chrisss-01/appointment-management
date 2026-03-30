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
        if (!Schema::hasColumn('medical_records', 'visual_acuity')) {
            Schema::table('medical_records', function (Blueprint $table) {
                $table->json('visual_acuity')->nullable()->after('vital_signs');
            });
        }
        
        // SQLite doesn't need enum conversion, and MySQL might fail if it's already a string.
        // We will just leave the record_type as is since Eloquent handles it gracefully.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('medical_records', 'visual_acuity')) {
            Schema::table('medical_records', function (Blueprint $table) {
                $table->dropColumn('visual_acuity');
            });
        }
    }
};
