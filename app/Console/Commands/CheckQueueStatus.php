<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckQueueStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of queued jobs';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('📊 Queue Status Report');
        $this->line('');

        // Pending jobs
        $pending = DB::table('jobs')->where('reserved_at', null)->count();
        $this->info("⏳ Pending Jobs: {$pending}");

        // Reserved jobs (being processed)
        $reserved = DB::table('jobs')->whereNotNull('reserved_at')->count();
        $this->info("🔄 Reserved Jobs (Processing): {$reserved}");

        // Failed jobs
        $failed = DB::table('failed_jobs')->count();
        $this->warn("❌ Failed Jobs: {$failed}");

        // Total
        $total = $pending + $reserved;
        $this->line('');
        $this->info("Total in Queue: {$total}");

        // Show pending jobs details
        if ($pending > 0) {
            $this->line('');
            $this->info('📝 Pending Jobs Details:');
            $jobs = DB::table('jobs')->where('reserved_at', null)->select('id', 'queue', 'attempts', 'created_at')->get();
            $this->table(['ID', 'Queue', 'Attempts', 'Created At'], $jobs->toArray());
        }

        // Show failed jobs if any
        if ($failed > 0) {
            $this->line('');
            $this->warn('⚠️  Failed Jobs Details:');
            $failedJobs = DB::table('failed_jobs')->select('id', 'queue', 'failed_at')->latest('failed_at')->limit(5)->get();
            $this->table(['ID', 'Queue', 'Failed At'], $failedJobs->toArray());
        }
    }
}
