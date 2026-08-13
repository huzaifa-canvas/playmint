<?php

namespace App\Console\Commands;

use App\Models\Child;
use App\Models\QuizAttempt;
use App\Notifications\StreakBreakReminderNotification;
use Illuminate\Console\Command;

class SendStreakReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-streak-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send streak break reminders to children who haven\'t played today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        $children = Child::all();

        foreach ($children as $child) {
            $hasPlayedToday = QuizAttempt::where('child_id', $child->id)
                ->where('played_date', $today)
                ->exists();

            if (!$hasPlayedToday) {
                $notification = new StreakBreakReminderNotification($child);
                $child->notify($notification);
                
                if ($child->parent) {
                    $child->parent->notify($notification);
                }
            }
        }

        $this->info('Streak reminders sent successfully.');
    }
}
