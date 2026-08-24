<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Child;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalParents = User::where('is_admin', false)->count();
        $totalChildren = Child::count();
        $totalQuizzes = QuizAttempt::count();

        // Calculate average score by grade and subject
        // Score % = (correct_count / total_questions) * 100
        $avgScores = QuizAttempt::join('children', 'quiz_attempts.child_id', '=', 'children.id')
            ->join('grades', 'children.grade_id', '=', 'grades.id')
            ->join('subjects', 'quiz_attempts.subject_id', '=', 'subjects.id')
            ->select(
                'grades.name as grade_name',
                'subjects.name as subject_name',
                DB::raw('AVG((quiz_attempts.correct_count / NULLIF(quiz_attempts.total_questions, 0)) * 100) as average_score')
            )
            ->groupBy('grades.name', 'subjects.name')
            ->get();

        return view('content.pages.pages-home', compact(
            'totalParents',
            'totalChildren',
            'totalQuizzes',
            'avgScores'
        ));
    }
}
