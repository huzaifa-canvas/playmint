<?php

namespace App\Notifications;

use App\Models\Child;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MilestoneUnlockedNotification extends Notification
{
    use Queueable;

    public $child;
    public $milestoneName;

    public function __construct(Child $child, $milestoneName)
    {
        $this->child = $child;
        $this->milestoneName = $milestoneName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'milestone_unlocked',
            'title' => 'Milestone Unlocked! 🏆',
            'message' => "{$this->child->name} just unlocked the {$this->milestoneName} badge!",
            'child_id' => $this->child->id,
        ];
    }
}
