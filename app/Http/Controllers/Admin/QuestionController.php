<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        if ($request->has('difficulty') && $request->difficulty != '') {
            $query->where('difficulty', $request->difficulty);
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
            'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();
        $data['correct_option'] = strtolower($data['correct_option']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('questions', 'public');
            $data['image'] = 'storage/' . $path;
        }

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
            'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();
        $data['correct_option'] = strtolower($data['correct_option']);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($question->image) {
                $oldPath = str_replace('storage/', '', $question->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image')->store('questions', 'public');
            $data['image'] = 'storage/' . $path;
        }

        // If user explicitly removed the image
        if ($request->has('remove_image') && $request->remove_image) {
            if ($question->image) {
                $oldPath = str_replace('storage/', '', $question->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $data['image'] = null;
        }

        $question->update($data);
        $question->grades()->sync($request->grade_ids);

        return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        // Delete image file if exists
        if ($question->image) {
            $imagePath = str_replace('storage/', '', $question->image);
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $question->delete();
        return redirect()->route('admin.questions.index')->with('success', 'Question deleted successfully.');
    }
}
