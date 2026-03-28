<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_requests', function (Blueprint $table) {
            $table->renameColumn('purpose', 'purpose_type');
            $table->text('purpose_text')->nullable()->after('purpose_type');
        });

        // Normalise any legacy "__other__" sentinel values to "other"
        DB::table('certificate_requests')
            ->where('purpose_type', '__other__')
            ->update(['purpose_type' => 'other']);
    }

    public function down(): void
    {
        Schema::table('certificate_requests', function (Blueprint $table) {
            $table->dropColumn('purpose_text');
            $table->renameColumn('purpose_type', 'purpose');
        });
    }
};
