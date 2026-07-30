<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChildController extends Controller
{
    /**
     * List all children of the authenticated parent.
     */
    public function index()
    {
        $children = Auth::user()
            ->children()
            ->with(['avatar', 'grade', 'subjects'])
            ->get()
            ->map(fn ($c) => $this->formatChild($c));

        return response()->json([
            'status'   => true,
            'children' => $children,
        ]);
    }

    /**
     * Show a single child (must belong to authenticated parent).
     */
    public function show($id)
    {
        $child = Auth::user()->children()->with(['avatar', 'grade', 'subjects'])->find($id);

        if (!$child) {
            return response()->json(['status' => false, 'message' => 'Child not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'child'  => $this->formatChild($child),
        ]);
    }

    /**
     * Add a new child for the authenticated parent.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'age'         => 'nullable|integer|min:1|max:18',
            'avatar_id'   => 'nullable|exists:avatars,id',
            'grade_id'    => 'nullable|exists:grades,id',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
            'daily_reward_time_limit' => 'nullable|integer|min:1',
            'questions_per_quiz'      => 'nullable|integer|min:1',
            'quizzes_per_day'         => 'nullable|integer|min:1',
        ]);

        $child = Auth::user()->children()->create([
            'name'      => $request->name,
            'age'       => $request->age,
            'avatar_id' => $request->avatar_id,
            'grade_id'  => $request->grade_id,
            'daily_reward_time_limit' => $request->daily_reward_time_limit,
            'questions_per_quiz'      => $request->questions_per_quiz,
            'quizzes_per_day'         => $request->quizzes_per_day,
        ]);

        if ($request->filled('subject_ids')) {
            $child->subjects()->sync($request->subject_ids);
        }

        $child->load(['avatar', 'grade', 'subjects']);

        return response()->json([
            'status'  => true,
            'message' => 'Child profile created.',
            'child'   => $this->formatChild($child),
        ], 201);
    }

    /**
     * Update a child's profile.
     */
    public function update(Request $request, $id)
    {
        $child = Auth::user()->children()->find($id);

        if (!$child) {
            return response()->json(['status' => false, 'message' => 'Child not found.'], 404);
        }

        $request->validate([
            'name'        => 'sometimes|string|max:100',
            'age'         => 'nullable|integer|min:1|max:18',
            'avatar_id'   => 'nullable|exists:avatars,id',
            'grade_id'    => 'nullable|exists:grades,id',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
            'daily_reward_time_limit' => 'nullable|integer|min:1',
            'questions_per_quiz'      => 'nullable|integer|min:1',
            'quizzes_per_day'         => 'nullable|integer|min:1',
        ]);

        $child->update($request->only([
            'name', 'age', 'avatar_id', 'grade_id',
            'daily_reward_time_limit', 'questions_per_quiz', 'quizzes_per_day',
        ]));

        if ($request->has('subject_ids')) {
            $child->subjects()->sync($request->subject_ids ?? []);
        }

        $child->load(['avatar', 'grade', 'subjects']);

        return response()->json([
            'status'  => true,
            'message' => 'Child profile updated.',
            'child'   => $this->formatChild($child),
        ]);
    }

    /**
     * Delete a child profile.
     */
    public function destroy($id)
    {
        $child = Auth::user()->children()->find($id);

        if (!$child) {
            return response()->json(['status' => false, 'message' => 'Child not found.'], 404);
        }

        $child->subjects()->detach();
        $child->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Child profile deleted.',
        ]);
    }

    /**
     * Update quiz settings for a child.
     */
    public function updateSettings(Request $request, $id)
    {
        $child = Auth::user()->children()->find($id);

        if (!$child) {
            return response()->json(['status' => false, 'message' => 'Child not found.'], 404);
        }

        $request->validate([
            'daily_reward_time_limit' => 'nullable|integer|min:1',
            'questions_per_quiz'      => 'nullable|integer|min:1',
            'quizzes_per_day'         => 'nullable|integer|min:1',
        ]);

        $child->update($request->only([
            'daily_reward_time_limit', 'questions_per_quiz', 'quizzes_per_day',
        ]));

        $child->load(['avatar', 'grade', 'subjects']);

        return response()->json([
            'status'  => true,
            'message' => 'Child quiz settings updated.',
            'child'   => $this->formatChild($child),
        ]);
    }

    /**
     * Format a child model for the API response.
     */
    private function formatChild(Child $child): array
    {
        return [
            'id'       => $child->id,
            'name'     => $child->name,
            'age'      => (int) $child->age,
            'quiz_count' => (int) rand(1, 200),
            'settings' => [
                'daily_reward_time_limit' => $child->daily_reward_time_limit ? (int) $child->daily_reward_time_limit : null,
                'questions_per_quiz'      => $child->questions_per_quiz ? (int) $child->questions_per_quiz : null,
                'quizzes_per_day'         => $child->quizzes_per_day ? (int) $child->quizzes_per_day : null,
            ],
            'avatar'   => $child->avatar ? [
                'id'        => $child->avatar->id,
                'image_url' => $child->avatar->image_url,
            ] : null,
            'grade'    => $child->grade ? [
                'id'   => $child->grade->id,
                'name' => $child->grade->name,
            ] : null,
            'subjects' => $child->subjects->map(fn ($s) => [
                'id'   => $s->id,
                'name' => $s->name,
                'icon' => $s->icon ? asset('storage/' . $s->icon) : null,
            ])->values(),
        ];
    }
}
