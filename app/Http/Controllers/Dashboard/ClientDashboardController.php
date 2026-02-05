<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentRequirement;

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

        return view('dashboard.client', [
            'documents' => $documents,
            'requirements' => $requirements,
        ]);
    }
}
