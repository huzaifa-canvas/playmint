<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardTimeController extends Controller
{
    /**
     * Get the remaining reward time for a child.
     *
     * Mobile app hits this to start the countdown timer.
     */
    public function show($id)
    {
        $child = Auth::user()->children()->find($id);

        if (!$child) {
            return response()->json(['status' => false, 'message' => 'Child not found.'], 404);
        }

        $remainingSeconds = (int) $child->remaining_reward_seconds;

        // If reward_date is not today, the reward has expired (new day = reset)
        if (!$child->reward_date || $child->reward_date->toDateString() !== now()->toDateString()) {
            $remainingSeconds = 0;
        }

        $minutes = (int) floor($remainingSeconds / 60);
        $seconds = $remainingSeconds % 60;

        $displayText = $seconds > 0
            ? "{$minutes} Min {$seconds} Sec"
            : "{$minutes} Min";

        return response()->json([
            'status'                   => true,
            'remaining_reward_seconds' => $remainingSeconds,
            'remaining_display'        => $displayText,
            'reward_date'              => $child->reward_date ? $child->reward_date->toDateString() : null,
        ]);
    }

    /**
     * Save remaining reward time when child stops the countdown.
     *
     * Mobile app calls this when child pauses/exits the reward screen.
     * Sends back how many seconds are still remaining on the timer.
     */
    public function stop(Request $request, $id)
    {
        $child = Auth::user()->children()->find($id);

        if (!$child) {
            return response()->json(['status' => false, 'message' => 'Child not found.'], 404);
        }

        $request->validate([
            'remaining_seconds' => 'required|integer|min:0',
        ]);

        $submittedSeconds = (int) $request->remaining_seconds;

        // Safety: remaining_seconds should not exceed what's stored in DB
        $currentRemaining = (int) $child->remaining_reward_seconds;

        // If reward_date is not today, treat current as 0 (expired)
        if (!$child->reward_date || $child->reward_date->toDateString() !== now()->toDateString()) {
            $currentRemaining = 0;
        }

        // Clamp: cannot save more than what was available
        $submittedSeconds = min($submittedSeconds, $currentRemaining);

        $child->update([
            'remaining_reward_seconds' => $submittedSeconds,
            'reward_date'              => now()->toDateString(),
        ]);

        $minutes = (int) floor($submittedSeconds / 60);
        $seconds = $submittedSeconds % 60;

        $displayText = $seconds > 0
            ? "{$minutes} Min {$seconds} Sec"
            : "{$minutes} Min";

        return response()->json([
            'status'                   => true,
            'message'                  => 'Reward time updated.',
            'remaining_reward_seconds' => $submittedSeconds,
            'remaining_display'        => $displayText,
        ]);
    }
}
