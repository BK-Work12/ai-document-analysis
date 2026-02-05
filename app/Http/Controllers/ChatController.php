<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        // Get all documents for the user with their message count
        $documents = Document::where('user_id', $userId)
            ->with('messages')
            ->orderByDesc('created_at')
            ->get();

        $selectedDocument = null;
        if ($request->query('document_id')) {
            $selectedDocument = $documents->firstWhere('id', $request->query('document_id'));
        }

        return view('chats.index', [
            'documents' => $documents,
            'selectedDocument' => $selectedDocument,
        ]);
    }

    public function show(Request $request, Document $document)
    {
        // Authorize user
        if ($document->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $userId = $request->user()->id;
        
        $documents = Document::where('user_id', $userId)
            ->with('messages')
            ->orderByDesc('created_at')
            ->get();

        return view('chats.index', [
            'documents' => $documents,
            'selectedDocument' => $document,
        ]);
    }
}
