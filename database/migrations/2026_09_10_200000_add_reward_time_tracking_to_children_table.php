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
            $table->integer('remaining_reward_seconds')->default(0)->after('time_reward_per_question');
            $table->date('reward_date')->nullable()->after('remaining_reward_seconds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn(['remaining_reward_seconds', 'reward_date']);
        });
    }
};
