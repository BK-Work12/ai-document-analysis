<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentMessage;
use App\Models\DocumentRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get all required document types
        $requirements = DocumentRequirement::where('required', true)
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();

        // Get user's uploaded documents
        $documents = Document::where('user_id', $user->id)
            ->orderBy('doc_type')
            ->orderByDesc('version')
            ->get();

        $dashboardSignature = $this->buildDashboardSignature($user->id);

        return view('dashboard.client', [
            'documents' => $documents,
            'requirements' => $requirements,
            'dashboardSignature' => $dashboardSignature,
        ]);
    }

    public function heartbeat(Request $request)
    {
        $userId = $request->user()->id;

        return response()->json([
            'signature' => $this->buildDashboardSignature($userId),
        ]);
    }

    private function buildDashboardSignature(int $userId): string
    {
        $docUpdatedAt = Document::where('user_id', $userId)->max('updated_at');
        $messageUpdatedAt = DocumentMessage::whereHas('document', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->max('updated_at');

        $docTs = $docUpdatedAt ? Carbon::parse($docUpdatedAt)->timestamp : 0;
        $messageTs = $messageUpdatedAt ? Carbon::parse($messageUpdatedAt)->timestamp : 0;

        return (string) max($docTs, $messageTs);
    }
}
