<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\DB;

class JobTrackingController extends Controller
{
    public function index()
    {
        // Get jobs from the jobs table with decoded payloads
        $jobsQuery = DB::table('jobs')
            ->orderByDesc('created_at');
        
        $jobs = $jobsQuery->paginate(20);

        // Decode job payloads while keeping pagination
        foreach ($jobs as $job) {
            $job->decoded_payload = json_decode($job->payload, true);
        }

        // Get failed jobs from failed_jobs table with decoded payloads
        $failedJobsQuery = DB::table('failed_jobs')
            ->orderByDesc('failed_at');
        
        $failedJobs = $failedJobsQuery->paginate(20);

        // Decode failed job payloads while keeping pagination
        foreach ($failedJobs as $job) {
            $job->decoded_payload = json_decode($job->payload, true);
        }

        // Document processing history (acts as end-to-end job tracking)
        $documentJobs = Document::query()
            ->with('user:id,name,email')
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->paginate(20, ['*'], 'document_jobs_page');

        // Get job statistics
        $stats = [
            'total_jobs' => DB::table('jobs')->count(),
            'failed_jobs' => DB::table('failed_jobs')->count(),
            'processed_today' => DB::table('jobs')
                ->where('created_at', '>=', now()->startOfDay())
                ->count(),
            'documents_processing' => Document::query()
                ->where(function ($query) {
                    $query->whereIn('extraction_status', ['pending', 'processing'])
                        ->orWhereIn('analysis_status', ['pending', 'processing']);
                })
                ->count(),
            'documents_completed' => Document::query()
                ->where('extraction_status', 'completed')
                ->where('analysis_status', 'completed')
                ->count(),
            'documents_failed' => Document::query()
                ->where(function ($query) {
                    $query->where('extraction_status', 'failed')
                        ->orWhere('analysis_status', 'failed');
                })
                ->count(),
        ];

        return view('admin.jobs.tracking', [
            'jobs' => $jobs,
            'failedJobs' => $failedJobs,
            'documentJobs' => $documentJobs,
            'stats' => $stats,
        ]);
    }

    public function retryFailed($jobId)
    {
        $failedJob = DB::table('failed_jobs')->where('id', $jobId)->first();

        if (!$failedJob) {
            return back()->with('error', 'Failed job not found.');
        }

        // Re-queue the failed job
        DB::table('jobs')->insert([
            'queue' => $failedJob->queue ?? 'default',
            'payload' => $failedJob->payload,
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => now()->timestamp,
            'created_at' => now()->timestamp,
        ]);

        // Delete from failed_jobs
        DB::table('failed_jobs')->where('id', $jobId)->delete();

        return back()->with('success', 'Job has been re-queued.');
    }

    public function clearFailed()
    {
        DB::table('failed_jobs')->delete();

        return back()->with('success', 'All failed jobs have been cleared.');
    }
}
