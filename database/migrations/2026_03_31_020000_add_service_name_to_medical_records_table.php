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
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('service_name')->nullable()->after('appointment_id');
        });

        // Backfill existing records
        DB::table('medical_records')->whereNotNull('appointment_id')->orderBy('id')->chunk(100, function ($records) {
            foreach ($records as $record) {
                $appointment = DB::table('appointments')->where('id', $record->appointment_id)->first();
                if ($appointment) {
                    $service = DB::table('services')->where('id', $appointment->service_id)->first();
                    if ($service) {
                        DB::table('medical_records')
                            ->where('id', $record->id)
                            ->update(['service_name' => $service->name]);
                    }
                }
            }
        });
        
        // Backfill records without appointments
        DB::statement("UPDATE medical_records SET service_name = 'Consultation' WHERE service_name IS NULL AND record_type = 'consultation'");
        DB::statement("UPDATE medical_records SET service_name = 'Dental' WHERE service_name IS NULL AND record_type = 'dental'");
        DB::statement("UPDATE medical_records SET service_name = 'General' WHERE service_name IS NULL AND record_type = 'general'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn('service_name');
        });
    }
};
