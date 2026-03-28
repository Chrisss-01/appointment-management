<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('certificate_type_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number')->nullable()->unique();
            $table->text('purpose')->nullable();
            $table->text('additional_notes')->nullable();
            $table->enum('status', [
                'pending',
                'documents_verified',
                'approved',
                'rejected',
            ])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete(); // nurse
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // doctor
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('file_path')->nullable(); // generated PDF
            $table->string('qr_code')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index('certificate_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_requests');
    }
};
