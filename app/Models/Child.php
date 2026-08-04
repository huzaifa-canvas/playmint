<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'avatar_id', 'name', 'age', 'grade_id',
        'daily_reward_time_limit', 'questions_per_quiz', 'quizzes_per_day',
        'duration_per_quiz', 'daily_quiz_reminders', 'weekly_progress_report', 'reward_time_alerts',
    ];

    protected $casts = [
        'daily_quiz_reminders'    => 'boolean',
        'weekly_progress_report'  => 'boolean',
        'reward_time_alerts'      => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function avatar()
    {
        return $this->belongsTo(Avatar::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'child_subject');
    }
}
