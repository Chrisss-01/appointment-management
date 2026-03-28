<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('staff_type')->nullable()->after('role'); // doctor, nurse
            $table->string('signature_image')->nullable()->after('avatar');
            $table->string('license_number')->nullable()->after('signature_image');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['staff_type', 'signature_image', 'license_number']);
        });
    }
};
