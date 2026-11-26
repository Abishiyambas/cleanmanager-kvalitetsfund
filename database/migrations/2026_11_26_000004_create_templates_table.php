<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kontrolskabelon: en navngiven liste af kontrolpunkter
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->text('description')->nullable();
        });

        Schema::create('template_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('template_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained();
            $table->string('text');
            $table->unsignedInteger('position')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_items');
        Schema::dropIfExists('templates');
    }
};
