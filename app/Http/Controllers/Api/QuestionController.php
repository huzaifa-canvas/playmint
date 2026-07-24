<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    /**
     * Display a listing of the questions.
     * Can filter by grade_id and subject_id
     */
    public function index(Request $request)
    {
        $query = Question::with(['subject', 'grades']);

        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('grade_id') && $request->grade_id != '') {
            $gradeId = $request->grade_id;
            $query->whereHas('grades', function ($q) use ($gradeId) {
                $q->where('grades.id', $gradeId);
            });
        }

        $questions = $query->inRandomOrder()->paginate(20);
        return response()->json(['status' => true, 'data' => $questions]);
    }

    /**
     * Display the specified question.
     */
    public function show($id)
    {
        $question = Question::with(['subject', 'grade'])->find($id);

        if (!$question) {
            return response()->json(['status' => false, 'message' => 'Question not found.'], 404);
        }

        return response()->json(['status' => true, 'data' => $question]);
    }
}
