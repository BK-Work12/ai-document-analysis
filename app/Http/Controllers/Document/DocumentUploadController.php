<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentUploadRequest;
use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentUploadedNotification;
use App\Events\DocumentUploaded;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentUploadController extends Controller
{
    public function store(StoreDocumentUploadRequest $request)
    {
        $user = auth()->user();
        $file = $request->file('file');
        $docType = $request->input('doc_type');

        // Get or create the next version
        $latestVersion = Document::where('user_id', $user->id)
            ->where('doc_type', $docType)
            ->max('version') ?? 0;

        $version = $latestVersion + 1;
        $uuid = Str::uuid();
        $originalName = $file->getClientOriginalName();
        $storagePath = "clients/{$user->id}/{$docType}/v{$version}";
        $fileName = "{$uuid}-{$originalName}";

        // Determine which disk to use (S3 if configured, otherwise local)
        $disk = $this->getConfiguredDisk();
        
        // Store file
        $path = Storage::disk($disk)->putFileAs(
            $storagePath,
            $file,
            $fileName,
            ['visibility' => 'private']
        );

        // Create or update document record
        $document = Document::updateOrCreate(
            ['user_id' => $user->id, 'doc_type' => $docType],
            [
                's3_key' => $path,
                'version' => $version,
                'status' => 'pending',
                'original_filename' => $originalName,
                'detected_mime' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
                'uploaded_at' => now(),
            ]
        );

        // Dispatch event for listeners to process
        DocumentUploaded::dispatch($document);

        // Notify admins about new upload
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new DocumentUploadedNotification($document, $user));
        }

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'document' => $document,
        ], 201);
    }

    public function download($documentId)
    {
        $document = Document::findOrFail($documentId);
        
        // Check authorization
        if (auth()->user()->id !== $document->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized to download this document');
        }

        $disk = $this->getConfiguredDisk();
        
        if ($disk === 's3') {
            $url = Storage::disk('s3')->temporaryUrl($document->s3_key, now()->addMinutes(15));

            return response()->json([
                'download_url' => $url,
                'expires_in' => 15,
                'encrypted' => true,
                'encryption' => 'AWS KMS',
            ]);
        }

        // Local disk: stream the file download to avoid public URLs
        if (!Storage::disk('local')->exists($document->s3_key)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return Storage::disk('local')->download(
            $document->s3_key,
            $document->original_filename ?? basename($document->s3_key)
        );
    }

    private function getConfiguredDisk()
    {
        // Check if local storage is explicitly enabled
        if (env('USE_LOCAL_STORAGE', true)) {
            return 'local';
        }
        
        // Use S3 if configured
        if (env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_BUCKET')) {
            return 's3';
        }
        
        // Fallback to local storage if S3 is not configured
        return 'local';
    }

    public function preview($documentId)
    {
        $document = Document::findOrFail($documentId);

        // Check authorization
        if (auth()->user()->id !== $document->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized to preview this document');
        }

        $disk = $this->getConfiguredDisk();

        // For S3, redirect to a short-lived signed URL suitable for inline viewing
        if ($disk === 's3') {
            $url = Storage::disk('s3')->temporaryUrl($document->s3_key, now()->addMinutes(10), [
                'ResponseContentDisposition' => 'inline; filename="' . ($document->original_filename ?? basename($document->s3_key)) . '"'
            ]);
            return redirect()->away($url);
        }

        // Local disk: stream inline
        if (!Storage::disk('local')->exists($document->s3_key)) {
            abort(404, 'File not found');
        }

        $path = Storage::disk('local')->path($document->s3_key);
        $filename = $document->original_filename ?? basename($path);

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}