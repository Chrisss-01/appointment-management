<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('generated_slot_id')->constrained('generated_slots')->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'completed',
                'cancelled',
                'no_show',
            ])->default('pending');
            $table->text('reason')->nullable();
            $table->text('staff_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->integer('queue_number')->nullable();
            $table->timestamps();

            $table->index(['date', 'status']);
            $table->index(['student_id', 'status']);
            $table->index(['staff_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
