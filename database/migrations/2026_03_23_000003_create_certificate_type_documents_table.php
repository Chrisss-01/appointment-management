<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_type_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_type_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Valid ID", "Medical Record"
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_type_documents');
    }
};
