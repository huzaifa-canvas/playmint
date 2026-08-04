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
            $table->unsignedInteger('duration_per_quiz')->nullable()->after('quizzes_per_day')->comment('Duration per quiz in minutes');
            $table->boolean('daily_quiz_reminders')->default(true)->after('duration_per_quiz');
            $table->boolean('weekly_progress_report')->default(true)->after('daily_quiz_reminders');
            $table->boolean('reward_time_alerts')->default(true)->after('weekly_progress_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn(['duration_per_quiz', 'daily_quiz_reminders', 'weekly_progress_report', 'reward_time_alerts']);
        });
    }
};
