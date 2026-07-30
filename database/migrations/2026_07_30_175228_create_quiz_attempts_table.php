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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('incorrect_count')->default(0);
            $table->unsignedInteger('duration')->default(0)->comment('Duration in seconds');
            $table->date('played_date')->index()->comment('Date the quiz was played');
            $table->json('question_ids')->comment('JSON array of attempted question IDs');
            $table->timestamps();

            // Composite indexes for efficient stats queries
            $table->index(['child_id', 'played_date']);
            $table->index(['child_id', 'subject_id']);
            $table->index(['child_id', 'subject_id', 'played_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
