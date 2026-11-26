<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('location_id')->constrained();
            $table->foreignId('template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inspector_id')->constrained('users');
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
