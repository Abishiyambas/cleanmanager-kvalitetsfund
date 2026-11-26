<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Korrigerende handling: altid knyttet til et fund, en ansvarlig og en frist
        Schema::create('corrective_actions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('finding_id')->constrained()->onDelete('cascade');
            $table->foreignId('responsible_id')->constrained('users');
            $table->foreignId('assigned_by')->constrained('users');
            $table->text('description');
            $table->date('due_date');
            $table->string('status')->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
        });

        // Bevis for udført arbejde, indsendt af den ansvarlige
        Schema::create('evidence', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('corrective_action_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->text('note');
            $table->string('photo_path')->nullable();
        });

        // Ændringshistorik: hver statusændring og hændelse på en handling
        Schema::create('action_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('corrective_action_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('event');
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->text('comment')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_logs');
        Schema::dropIfExists('evidence');
        Schema::dropIfExists('corrective_actions');
    }
};
