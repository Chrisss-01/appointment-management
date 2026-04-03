<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('status', [
                'pending', 'approved', 'rejected', 'completed',
                'cancelled', 'cancelled_by_staff', 'no_show',
            ])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('status', [
                'pending', 'approved', 'rejected', 'completed',
                'cancelled', 'no_show',
            ])->default('pending')->change();
        });
    }
};
