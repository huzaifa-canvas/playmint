<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    /**
     * Display a listing of the questions.
     * Requires child_id to filter by child's enrolled grade.
     * Can also filter by subject_id.
     */
    public function index(Request $request)
    {
        $request->validate([
            'child_id'   => 'required|exists:children,id',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        // Ensure the child belongs to the authenticated user
        $child = Auth::user()->children()->find($request->child_id);

        if (!$child) {
            return response()->json([
                'status'  => false,
                'message' => 'Child not found or does not belong to you.',
            ], 404);
        }

        if (!$child->grade_id) {
            return response()->json([
                'status'  => false,
                'message' => 'Child is not enrolled in any grade.',
            ], 422);
        }

        // Filter questions by the child's enrolled grade
        $query = Question::with(['subject'])
            ->whereHas('grades', function ($q) use ($child) {
                $q->where('grades.id', $child->grade_id);
            });

        // Optional subject filter
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        $questions = $query->inRandomOrder()->paginate(20);

        // Format: cast subject_id to int, group options, include only the child's grade
        $questions->getCollection()->transform(function ($question) use ($child) {
            $question->subject_id = (int) $question->subject_id;
            $question->options = [
                'a' => $question->option_a,
                'b' => $question->option_b,
                'c' => $question->option_c,
                'd' => $question->option_d,
            ];
            unset($question->option_a, $question->option_b, $question->option_c, $question->option_d);
            $question->image = $question->image ? asset($question->image) : null;
            $question->grade = $child->grade;
            unset($question->grades);
            return $question;
        });

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
