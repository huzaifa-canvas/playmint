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
        Schema::table('children', function (Blueprint $table) {
            $table->unsignedInteger('daily_reward_time_limit')->nullable()->after('grade_id')->comment('Daily reward time limit in minutes');
            $table->unsignedInteger('questions_per_quiz')->nullable()->after('daily_reward_time_limit')->comment('Number of questions per quiz session');
            $table->unsignedInteger('quizzes_per_day')->nullable()->after('questions_per_quiz')->comment('Max quizzes a child can attempt per day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn(['daily_reward_time_limit', 'questions_per_quiz', 'quizzes_per_day']);
        });
    }
};
