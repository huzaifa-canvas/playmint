<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Milestone;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     * Get dashboard data for a child.
     */
    public function dashboard(Request $request, $id = null)
    {
        $childId = $id ?? $request->child_id;

        $child = Auth::user()->children()->with(['avatar', 'grade', 'subjects'])->find($childId);

        if (!$child) {
            return response()->json(['status' => false, 'message' => 'Child not found.'], 404);
        }

        // 1. Total Quizzes & Points
        $totalQuizzesPlayed = QuizAttempt::where('child_id', $child->id)->count();
        $totalCorrectAnswers = QuizAttempt::where('child_id', $child->id)->sum('correct_count');
        $points = $totalCorrectAnswers * 10;

        // 2. Milestone Progress
        $currentMilestone = Milestone::where('start_range', '<=', max(1, $totalQuizzesPlayed))
            ->where('end_range', '>=', $totalQuizzesPlayed)
            ->first();

        if (!$currentMilestone) {
            $currentMilestone = Milestone::where('end_range', '<', $totalQuizzesPlayed)->orderBy('end_range', 'desc')->first();
        }

        $nextMilestone = Milestone::where('start_range', '>', $currentMilestone ? $currentMilestone->end_range : $totalQuizzesPlayed)
            ->orderBy('start_range', 'asc')
            ->first();

        if (!$nextMilestone) {
            $nextMilestone = Milestone::orderBy('end_range', 'desc')->first();
        }

        $targetQuizzes = $nextMilestone ? $nextMilestone->end_range : ($currentMilestone ? $currentMilestone->end_range : 100);
        $milestoneTitle = $nextMilestone ? $nextMilestone->name : ($currentMilestone ? $currentMilestone->name : 'Grandmaster');

        $milestoneData = [
            'current_badge' => $currentMilestone ? [
                'id'          => $currentMilestone->id,
                'name'        => $currentMilestone->name,
                'image'       => $currentMilestone->image ? asset($currentMilestone->image) : null,
                'start_range' => (int) $currentMilestone->start_range,
                'end_range'   => (int) $currentMilestone->end_range,
            ] : null,
            'next_badge' => $nextMilestone ? [
                'id'             => $nextMilestone->id,
                'name'           => $nextMilestone->name,
                'target_quizzes' => (int) $nextMilestone->end_range,
            ] : null,
            'completed_quizzes' => (int) $totalQuizzesPlayed,
            'target_quizzes'    => (int) $targetQuizzes,
            'progress_text'     => "{$totalQuizzesPlayed} / {$targetQuizzes} Quizzes To {$milestoneTitle}",
        ];

        // 3. Reward Time (Static as requested)
        $rewardTimeLimit = $child->daily_reward_time_limit ? (int) $child->daily_reward_time_limit : 45;
        $rewardTimeData = [
            'earned_minutes' => 25, // Static as requested
            'total_minutes'  => $rewardTimeLimit,
            'display_text'   => "25 / {$rewardTimeLimit} Min",
        ];

        // 4. Daily / Weekly Streak (Monday to Sunday of current week)
        $startOfWeek = now()->startOfWeek(); // Monday
        $todayStr = now()->toDateString();

        $weekDays = [];
        $dayInitials = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
        $streakActiveDays = 0;

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dateStr = $date->toDateString();

            $playedOnDay = QuizAttempt::where('child_id', $child->id)
                ->where('played_date', $dateStr)
                ->exists();

            if ($playedOnDay) {
                $streakActiveDays++;
            }

            $weekDays[] = [
                'day'       => $dayInitials[$i],
                'day_name'  => $date->format('D'),
                'date'      => $dateStr,
                'is_played' => $playedOnDay,
                'is_today'  => ($dateStr === $todayStr),
            ];
        }

        // 5. Today's Quizzes
        $todayAttempts = QuizAttempt::where('child_id', $child->id)
            ->where('played_date', $todayStr)
            ->with('subject')
            ->latest()
            ->get();

        $quizzesPerDay = $child->quizzes_per_day ? (int) $child->quizzes_per_day : 5;
        $playedTodayCount = $todayAttempts->count();
        $quizzesLeft = max(0, $quizzesPerDay - $playedTodayCount);

        $todayPlayedList = $todayAttempts->map(function ($attempt) use ($child) {
            return [
                'id'              => (int) $attempt->id,
                'subject_id'      => (int) $attempt->subject_id,
                'subject_name'    => $attempt->subject ? $attempt->subject->name : null,
                'subject_icon'    => ($attempt->subject && $attempt->subject->icon) ? asset('storage/' . str_replace(['public/', 'storage/'], '', $attempt->subject->icon)) : null,
                'grade_name'      => $child->grade ? $child->grade->name : null,
                'total_questions' => (int) $attempt->total_questions,
                'correct_count'   => (int) $attempt->correct_count,
                'incorrect_count' => (int) $attempt->incorrect_count,
                'duration'        => (int) $attempt->duration,
                'duration_min'    => (int) ceil($attempt->duration / 60),
                'played_at'       => $attempt->created_at->format('H:i A'),
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => [
                'child' => [
                    'id'     => (int) $child->id,
                    'name'   => $child->name,
                    'avatar' => $child->avatar ? [
                        'id'        => (int) $child->avatar->id,
                        'image_url' => $child->avatar->image_url,
                    ] : null,
                    'grade'  => $child->grade ? [
                        'id'   => $child->grade->id,
                        'name' => $child->grade->name,
                    ] : null,
                    'points' => $points,
                ],
                'milestone_badge' => $milestoneData,
                'reward_time'     => $rewardTimeData,
                'daily_streak'    => [
                    'total_days_active' => $streakActiveDays,
                    'streak_text'       => "{$streakActiveDays} Days!",
                    'days'              => $weekDays,
                ],
                'todays_quizzes'  => [
                    'quizzes_per_day_limit' => (int) $quizzesPerDay,
                    'played_count'          => (int) $playedTodayCount,
                    'quizzes_left'          => (int) $quizzesLeft,
                    'quizzes_left_text'     => "{$quizzesLeft} left",
                    'played_quizzes'        => $todayPlayedList,
                ],
            ],
        ]);
    }

    /**
     * Get Leaderboard for children (Weekly / Monthly / All-time).
     */
    public function leaderboard(Request $request)
    {
        $request->validate([
            'type'     => 'nullable|in:weekly,monthly,all_time',
            'child_id' => 'nullable|exists:children,id',
        ]);

        $type = $request->input('type', 'weekly');
        $currentChildId = $request->input('child_id');

        // Date filter
        $query = QuizAttempt::query();

        if ($type === 'weekly') {
            $query->where('played_date', '>=', now()->startOfWeek()->toDateString());
        } elseif ($type === 'monthly') {
            $query->where('played_date', '>=', now()->startOfMonth()->toDateString());
        }

        // Sum correct_count grouped by child_id
        $scores = $query->select('child_id', DB::raw('SUM(correct_count) as total_points'))
            ->groupBy('child_id')
            ->pluck('total_points', 'child_id');

        // Fetch all children belonging to the current parent with avatars
        $children = Auth::user()->children()->with('avatar')->get();

        $rankings = $children->map(function ($child) use ($scores, $currentChildId) {
            $points = (int) ($scores[$child->id] ?? 0);
            return [
                'id'               => (int)$child->id,
                'name'             => $child->name,
                'display_name'     => ((int)$child->id === (int)$currentChildId) ? $child->name . ' (You!)' : $child->name,
                'is_current_child' => ((int)$child->id === (int)$currentChildId),
                'points'           => $points * 10,
                'avatar'           => $child->avatar ? [
                    'id'        => (int)$child->avatar->id,
                    'image_url' => $child->avatar->image_url,
                ] : null,
            ];
        })
        ->sortByDesc('points')
        ->values();

        // Assign ranks (1-indexed)
        $rankings->transform(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        // Top 3 Podium
        $top3 = [
            'first'  => $rankings->firstWhere('rank', 1) ?? null,
            'second' => $rankings->firstWhere('rank', 2) ?? null,
            'third'  => $rankings->firstWhere('rank', 3) ?? null,
        ];

        return response()->json([
            'status' => true,
            'filter' => $type,
            'data'   => [
                'top_3'    => $top3,
                'rankings' => $rankings,
            ],
        ]);
    }

    /**
     * Get Control Centre data for a child (Performance, Progress, Screen Time).
     */
    public function controlCentre(Request $request, $id = null)
    {
        if ($id) {
            $child = Auth::user()->children()->with(['avatar', 'grade'])->find($id);
        } else {
            $child = Auth::user()->children()->with(['avatar', 'grade'])->first();
        }

        if (!$child) {
            return response()->json(['status' => false, 'message' => 'No child profile found. Please add a child first.'], 404);
        }

        // --- Siblings List with Levels ---
        $siblings = Auth::user()->children()->with('avatar')->get()->map(function ($sib) use ($child) {
            $sibQuizzes = QuizAttempt::where('child_id', $sib->id)->count();
            $level = max(1, (int) floor($sibQuizzes / 10)); // simple level logic
            return [
                'id'        => $sib->id,
                'name'      => $sib->name,
                'avatar'    => $sib->avatar ? [
                    'id'        => $sib->avatar->id,
                    'image_url' => $sib->avatar->image_url,
                ] : null,
                'level'     => $level,
                'is_active' => $sib->id === $child->id,
            ];
        });

        // --- Performance Tab ---
        // This Week
        $startOfWeek = now()->startOfWeek()->toDateString();
        $weeklyAttempts = QuizAttempt::where('child_id', $child->id)
            ->where('played_date', '>=', $startOfWeek)
            ->get();

        $weeklyQuizzesCount = $weeklyAttempts->count();
        $weeklyTotalQuestions = $weeklyAttempts->sum('total_questions');
        $weeklyCorrectAnswers = $weeklyAttempts->sum('correct_count');
        $weeklyAccuracy = $weeklyTotalQuestions > 0 ? round(($weeklyCorrectAnswers / $weeklyTotalQuestions) * 100) : 0;
        $weeklyDurationSecs = $weeklyAttempts->sum('duration');
        $weeklyStudyTimeHours = round($weeklyDurationSecs / 3600, 1);

        // All Time
        $allAttempts = QuizAttempt::where('child_id', $child->id)->get();
        $allTotalQuestions = $allAttempts->sum('total_questions');
        $allCorrectAnswers = $allAttempts->sum('correct_count');
        $allAccuracy = $allTotalQuestions > 0 ? round(($allCorrectAnswers / $allTotalQuestions) * 100) : 0;
        
        $gradeName = $child->grade ? $child->grade->name : 'their grade';
        $performanceRemark = "{$child->name} Is Performing Above Average For {$gradeName}!";

        // Subject Breakdown (All Time)
        $subjectBreakdown = QuizAttempt::where('child_id', $child->id)
            ->with('subject')
            ->select('subject_id', DB::raw('SUM(correct_count) as sum_correct'), DB::raw('SUM(total_questions) as sum_total'))
            ->groupBy('subject_id')
            ->get()
            ->map(function ($item) {
                $acc = $item->sum_total > 0 ? round(($item->sum_correct / $item->sum_total) * 100) : 0;
                return [
                    'name'                => $item->subject ? $item->subject->name : 'Unknown',
                    'accuracy_percentage' => $acc,
                ];
            });

        // --- Progress Tab ---
        $totalQuizzesAllTime = $allAttempts->count();
        $consistencyText = "Great Consistency This Week!";
        
        $dailyBreakdown = [];
        for ($i = 0; $i < 7; $i++) {
            $dateObj = now()->startOfWeek()->addDays($i);
            $dateStr = $dateObj->toDateString();
            $count = QuizAttempt::where('child_id', $child->id)->where('played_date', $dateStr)->count();
            
            $dailyBreakdown[] = [
                'day'     => $dateObj->format('D'), // Mon, Tue...
                'quizzes' => $count,
            ];
        }

        // --- Screen Time Tab ---
        $earnedMinutes = $child->daily_reward_time_limit ? (int) $child->daily_reward_time_limit : 45;
        $usedMinutes = 30; // Static for now as requested in previous similar logic

        return response()->json([
            'status' => true,
            'data'   => [
                'siblings'    => $siblings,
                'performance' => [
                    'this_week' => [
                        'quizzes_count'       => $weeklyQuizzesCount,
                        'accuracy_percentage' => $weeklyAccuracy,
                        'study_time_hours'    => $weeklyStudyTimeHours,
                    ],
                    'all_time' => [
                        'accuracy_percentage' => $allAccuracy,
                        'remark'              => $performanceRemark,
                        'subjects'            => $subjectBreakdown,
                    ]
                ],
                'progress' => [
                    'quizzes_completed' => $totalQuizzesAllTime,
                    'consistency_text'  => $consistencyText,
                    'daily_breakdown'   => $dailyBreakdown,
                ],
                'screen_time' => [
                    'earned_minutes' => $earnedMinutes,
                    'used_minutes'   => $usedMinutes,
                ],
            ]
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
            'quiz_count' => QuizAttempt::where('child_id', $child->id)->count(),
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