<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_purpose_presets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_type_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_purpose_presets');
    }
};
