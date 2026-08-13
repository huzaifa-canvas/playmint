<?php

namespace App\Notifications;

use App\Models\Child;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubjectStruggleNotification extends Notification
{
    use Queueable;

    public $child;
    public $subjectName;

    public function __construct(Child $child, $subjectName)
    {
        $this->child = $child;
        $this->subjectName = $subjectName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'subject_struggle',
            'title' => 'Subject Struggle Alert ⚠️',
            'message' => "{$this->child->name} might need some help with {$this->subjectName}.",
            'child_id' => $this->child->id,
        ];
    }
}
