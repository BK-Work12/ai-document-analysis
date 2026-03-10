<?php

namespace App\Console\Commands;

use App\Jobs\DocumentAnalysisJob;
use App\Jobs\TextExtractJob;
use App\Models\Document;
use Illuminate\Console\Command;

class ResolveStalePendingDocumentsCommand extends Command
{
    protected $signature = 'documents:resolve-stale-pending
                            {--minutes=20 : Minimum age (minutes) before a pending/processing document is considered stale}
                            {--hours= : Backward-compatible override in hours (deprecated)}';

    protected $description = 'Resolve stale pending documents by re-queuing stuck jobs and moving failed/stale records to needs_correction';

    public function handle(): int
    {
        $hoursOverride = $this->option('hours');
        $minutes = is_null($hoursOverride)
            ? max(5, (int) $this->option('minutes'))
            : max(1, (int) $hoursOverride) * 60;

        $threshold = now()->subMinutes($minutes);

        $documents = Document::query()
            ->where('status', 'pending')
            ->where(function ($query) use ($threshold) {
                $query
                    ->where('uploaded_at', '<=', $threshold)
                    ->orWhere(function ($inner) use ($threshold) {
                        $inner->whereIn('extraction_status', ['pending', 'processing'])
                            ->where('extraction_started_at', '<=', $threshold);
                    })
                    ->orWhere(function ($inner) use ($threshold) {
                        $inner->whereIn('analysis_status', ['pending', 'processing'])
                            ->where('analysis_started_at', '<=', $threshold);
                    });
            })
            ->orderBy('uploaded_at')
            ->get();

        if ($documents->isEmpty()) {
            $this->info("No stale pending documents older than {$minutes} minute(s).");
            return self::SUCCESS;
        }

        $queuedExtraction = 0;
        $queuedAnalysis = 0;
        $markedCorrection = 0;

        foreach ($documents as $document) {
            $document->refresh();

            if ($document->status !== 'pending') {
                continue;
            }

            $isExtractionFailed = $document->extraction_status === 'failed';
            $isAnalysisFailed = $document->analysis_status === 'failed';

            $isDocx = in_array(strtolower((string) $document->detected_mime), [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ], true);

            if ($isExtractionFailed || $isAnalysisFailed) {
                if ($isDocx && $isExtractionFailed) {
                    $document->update([
                        'extraction_status' => 'pending',
                        'analysis_status' => 'pending',
                        'extraction_error' => null,
                        'analysis_error' => null,
                    ]);

                    TextExtractJob::dispatch($document);
                    $queuedExtraction++;
                    continue;
                }

                $feedback = $document->analysis_error
                    ?: $document->extraction_error
                    ?: 'This document could not be processed automatically. Please review and re-upload if required.';

                $document->update([
                    'status' => 'needs_correction',
                    'correction_feedback' => $feedback,
                    'correction_requested_at' => now(),
                ]);

                $markedCorrection++;
                continue;
            }

            if ($document->extraction_status !== 'completed') {
                TextExtractJob::dispatch($document);
                $queuedExtraction++;
                continue;
            }

            if ($document->analysis_status !== 'completed') {
                DocumentAnalysisJob::dispatch($document);
                $queuedAnalysis++;
                continue;
            }

            $document->update([
                'status' => 'needs_correction',
                'correction_feedback' => 'Automated checks finished but could not reach an approval decision. Please review manually.',
                'correction_requested_at' => now(),
            ]);
            $markedCorrection++;
        }

        $this->info('Stale pending resolution complete.');
        $this->line("- Re-queued extraction: {$queuedExtraction}");
        $this->line("- Re-queued analysis: {$queuedAnalysis}");
        $this->line("- Marked needs_correction: {$markedCorrection}");

        return self::SUCCESS;
    }
}
