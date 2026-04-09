<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Update the status enum
            $table->enum('status', [
                'pending', 'approved', 'rejected', 'completed',
                'cancelled', 'cancelled_by_staff', 'no_show', 'expired'
            ])->default('pending')->change();
            
            // Add the expiry reason column
            $table->text('expiry_reason')->nullable()->after('rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('expiry_reason');
            
            $table->enum('status', [
                'pending', 'approved', 'rejected', 'completed',
                'cancelled', 'cancelled_by_staff', 'no_show',
            ])->default('pending')->change();
        });
    }
};
