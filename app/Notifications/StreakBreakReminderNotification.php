<?php

namespace App\Notifications;

use App\Models\Child;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StreakBreakReminderNotification extends Notification
{
    use Queueable;

    public $child;

    public function __construct(Child $child)
    {
        $this->child = $child;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'streak_break',
            'title' => 'Don\'t Break The Streak! 🔥',
            'message' => "{$this->child->name} hasn't played a quiz today. Play now to keep the streak alive!",
            'child_id' => $this->child->id,
        ];
    }
}
