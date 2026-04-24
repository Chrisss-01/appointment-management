<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            $this->rebuildAppointmentsTableForSqlite(nullableGeneratedSlotId: true, deleteAction: 'null');
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['generated_slot_id']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedBigInteger('generated_slot_id')->nullable()->change();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('generated_slot_id')
                ->references('id')
                ->on('generated_slots')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            if (DB::table('appointments')->whereNull('generated_slot_id')->exists()) {
                return;
            }

            $this->rebuildAppointmentsTableForSqlite(nullableGeneratedSlotId: false, deleteAction: 'cascade');
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['generated_slot_id']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('generated_slot_id')
                ->references('id')
                ->on('generated_slots')
                ->cascadeOnDelete();
        });

        if (DB::table('appointments')->whereNull('generated_slot_id')->doesntExist()) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->unsignedBigInteger('generated_slot_id')->nullable(false)->change();
            });
        }
    }

    private function rebuildAppointmentsTableForSqlite(bool $nullableGeneratedSlotId, string $deleteAction): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('appointments_new', function (Blueprint $table) use ($nullableGeneratedSlotId, $deleteAction) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $generatedSlot = $table->foreignId('generated_slot_id');
            if ($nullableGeneratedSlotId) {
                $generatedSlot->nullable();
            }
            $generatedSlot = $generatedSlot->constrained('generated_slots');
            if ($deleteAction === 'null') {
                $generatedSlot->nullOnDelete();
            } else {
                $generatedSlot->cascadeOnDelete();
            }
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'completed',
                'cancelled',
                'cancelled_by_staff',
                'no_show',
                'expired',
            ])->default('pending');
            $table->text('reason')->nullable();
            $table->text('additional_comments')->nullable();
            $table->text('staff_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('expiry_reason')->nullable();
            $table->integer('queue_number')->nullable();
            $table->timestamps();

            $table->index(['date', 'status']);
            $table->index(['student_id', 'status']);
            $table->index(['staff_id', 'date']);
        });

        DB::statement('
            INSERT INTO appointments_new (
                id,
                student_id,
                staff_id,
                service_id,
                generated_slot_id,
                date,
                start_time,
                end_time,
                status,
                reason,
                additional_comments,
                staff_notes,
                rejection_reason,
                cancellation_reason,
                cancelled_at,
                completed_at,
                expiry_reason,
                queue_number,
                created_at,
                updated_at
            )
            SELECT
                id,
                student_id,
                staff_id,
                service_id,
                generated_slot_id,
                date,
                start_time,
                end_time,
                status,
                reason,
                additional_comments,
                staff_notes,
                rejection_reason,
                cancellation_reason,
                cancelled_at,
                completed_at,
                expiry_reason,
                queue_number,
                created_at,
                updated_at
            FROM appointments
        ');

        Schema::drop('appointments');
        Schema::rename('appointments_new', 'appointments');
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
