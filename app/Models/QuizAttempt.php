<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'subject_id',
        'question_ids',
        'total_questions',
        'correct_count',
        'incorrect_count',
        'duration',
        'played_date',
    ];

    protected $casts = [
        'question_ids' => 'array',
        'played_date'  => 'date',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
