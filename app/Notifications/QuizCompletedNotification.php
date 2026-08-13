<?php

namespace App\Notifications;

use App\Models\Child;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuizCompletedNotification extends Notification
{
    use Queueable;

    public $child;
    public $quizAttempt;

    public function __construct(Child $child, $quizAttempt)
    {
        $this->child = $child;
        $this->quizAttempt = $quizAttempt;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'quiz_completed',
            'title' => 'Quiz Completed!',
            'message' => "{$this->child->name} scored {$this->quizAttempt->correct_count} out of {$this->quizAttempt->total_questions}!",
            'child_id' => $this->child->id,
            'attempt_id' => $this->quizAttempt->id,
        ];
    }
}
