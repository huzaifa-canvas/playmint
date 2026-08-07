<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
    /**
     * Save a quiz attempt result.
     *
     * Expected payload:
     * {
     *   "child_id": 1,
     *   "subject_id": 2,
     *   "question_ids": [5, 8, 12, 15],
     *   "correct_count": 3,
     *   "incorrect_count": 1,
     *   "duration": 120
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'child_id'        => 'required|exists:children,id',
            'subject_id'      => 'required|exists:subjects,id',
            'question_ids'    => 'required|array|min:1',
            'question_ids.*'  => 'integer|exists:questions,id',
            'correct_count'   => 'required|integer|min:0',
            'incorrect_count' => 'required|integer|min:0',
            'duration'        => 'required|integer|min:0',
        ]);

        // Ensure child belongs to authenticated parent
        $child = Auth::user()->children()->find($request->child_id);
        if (!$child) {
            return response()->json([
                'status'  => false,
                'message' => 'Child not found or does not belong to you.',
            ], 404);
        }

        $attempt = QuizAttempt::create([
            'child_id'        => $request->child_id,
            'subject_id'      => $request->subject_id,
            'question_ids'    => $request->question_ids,
            'total_questions'  => count($request->question_ids),
            'correct_count'   => $request->correct_count,
            'incorrect_count' => $request->incorrect_count,
            'duration'        => $request->duration,
            'played_date'     => now()->toDateString(),
        ]);

        $quizzesPerDay = $child->quizzes_per_day ? (int) $child->quizzes_per_day : 0;
        $playedTodayCount = QuizAttempt::where('child_id', $child->id)
            ->where('played_date', now()->toDateString())
            ->count();
        $quizzesLeft = max(0, $quizzesPerDay - $playedTodayCount);

        return response()->json([
            'status'  => true,
            'message' => 'Quiz result saved successfully.',
            'data'    => [
                'attempt_id'            => $attempt->id,
                'total_questions'       => $attempt->total_questions,
                'correct_count'         => $attempt->correct_count,
                'incorrect_count'       => $attempt->incorrect_count,
                'accuracy'              => $attempt->total_questions > 0
                    ? round(($attempt->correct_count / $attempt->total_questions) * 100, 1)
                    : 0,
                'duration'              => $attempt->duration,
                'played_date'           => $attempt->played_date->toDateString(),
                'quizzes_per_day_limit' => $quizzesPerDay,
                'quizzes_played_today'  => $playedTodayCount,
                'quizzes_left_today'    => $quizzesLeft,
            ],
        ], 201);
    }
}
