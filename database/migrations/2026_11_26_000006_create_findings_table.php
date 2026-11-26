<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('inspection_id')->constrained()->onDelete('cascade');
            // location_id kopieres fra inspektionen, så "gentagne fund pr. lokation" er en simpel query
            $table->foreignId('location_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->string('severity'); // low | medium | high | critical
            $table->text('comment');
            $table->string('photo_path')->nullable();

            $table->index(['location_id', 'category_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};
