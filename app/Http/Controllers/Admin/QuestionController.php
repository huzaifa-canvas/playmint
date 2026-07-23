<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
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

        $questions = $query->paginate(10);
        $subjects = Subject::all();
        $grades = Grade::orderBy('order', 'asc')->get();

        return view('content.admin.questions.index', compact('questions', 'subjects', 'grades'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $grades = Grade::orderBy('order', 'asc')->get();
        return view('content.admin.questions.create', compact('subjects', 'grades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'grade_ids' => 'required|array',
            'grade_ids.*' => 'exists:grades,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_option' => ['required', Rule::in(['a', 'b', 'c', 'd', 'A', 'B', 'C', 'D'])],
        ]);

        $data = $request->all();
        $data['correct_option'] = strtolower($data['correct_option']);

        $question = Question::create($data);
        $question->grades()->sync($request->grade_ids);

        return redirect()->route('admin.questions.index')->with('success', 'Question created successfully.');
    }

    public function edit(Question $question)
    {
        $subjects = Subject::all();
        $grades = Grade::orderBy('order', 'asc')->get();
        return view('content.admin.questions.edit', compact('question', 'subjects', 'grades'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'grade_ids' => 'required|array',
            'grade_ids.*' => 'exists:grades,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_option' => ['required', Rule::in(['a', 'b', 'c', 'd', 'A', 'B', 'C', 'D'])],
        ]);

        $data = $request->all();
        $data['correct_option'] = strtolower($data['correct_option']);

        $question->update($data);
        $question->grades()->sync($request->grade_ids);

        return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('admin.questions.index')->with('success', 'Question deleted successfully.');
    }
}
