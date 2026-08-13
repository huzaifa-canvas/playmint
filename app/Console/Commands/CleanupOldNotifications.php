<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete notifications older than 15 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = Carbon::now()->subDays(15);
        
        $deletedCount = DB::table('notifications')
            ->where('created_at', '<', $threshold)
            ->delete();

        $this->info("Successfully deleted {$deletedCount} old notifications.");
    }
}
