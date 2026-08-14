<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get notifications for the authenticated user (parent) or a specific child.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->has('child_id')) {
            $child = $user->children()->find($request->child_id);
            
            if (!$child) {
                return response()->json([
                    'status' => false,
                    'message' => 'Child not found or unauthorized.'
                ], 404);
            }

            $notifications = $child->notifications()->paginate(20);
        } else {
            // Parent's notifications
            $notifications = $user->notifications()->paginate(20);
        }

        $formatted = $notifications->map(function ($notif) {
            return [
                'id' => $notif->id,
                'type' => $notif->data['type'] ?? null,
                'title' => $notif->data['title'] ?? 'Notification',
                'message' => $notif->data['message'] ?? '',
                'child_id' => $notif->data['child_id'] ?? null,
                'attempt_id' => $notif->data['attempt_id'] ?? null,
                'is_read' => $notif->read_at !== null,
                'created_at' => $notif->created_at->toIso8601String(),
                'created_at_human' => $notif->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formatted,
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ]
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        
        // Try finding in user's notifications
        $notification = $user->notifications()->where('id', $id)->first();

        // If not found, try children's notifications
        if (!$notification) {
            foreach ($user->children as $child) {
                $notification = $child->notifications()->where('id', $id)->first();
                if ($notification) {
                    break;
                }
            }
        }

        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found.'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read.'
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        // Try finding in user's notifications
        $notification = $user->notifications()->where('id', $id)->first();

        // If not found, try children's notifications
        if (!$notification) {
            foreach ($user->children as $child) {
                $notification = $child->notifications()->where('id', $id)->first();
                if ($notification) {
                    break;
                }
            }
        }

        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found.'
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'status' => true,
            'message' => 'Notification deleted.'
        ]);
    }

    /**
     * Get notification count (total and unread).
     */
    public function count(Request $request)
    {
        $user = Auth::user();

        if ($request->has('child_id')) {
            $child = $user->children()->find($request->child_id);
            
            if (!$child) {
                return response()->json([
                    'status' => false,
                    'message' => 'Child not found or unauthorized.'
                ], 404);
            }

            $total = $child->notifications()->count();
            $unread = $child->unreadNotifications()->count();
        } else {
            // Parent's notifications
            $total = $user->notifications()->count();
            $unread = $user->unreadNotifications()->count();
        }

        return response()->json([
            'status' => true,
            'data' => [
                'total' => $total,
                'unread' => $unread,
            ]
        ]);
    }
}
