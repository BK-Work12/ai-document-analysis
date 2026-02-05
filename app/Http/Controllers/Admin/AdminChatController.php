<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    public function list()
    {
        // Get all clients (non-admin users) with their documents
        $users = User::where('role', 'client')
            ->with(['documents' => function ($query) {
                $query->with('messages')->latest();
            }])
            ->latest('created_at')
            ->get();

        return view('admin.chats.list', [
            'users' => $users,
        ]);
    }

    public function index(User $user)
    {
        // Get all documents for the client with their message count
        $documents = Document::where('user_id', $user->id)
            ->with('messages')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.chats.index', [
            'user' => $user,
            'documents' => $documents,
            'selectedDocument' => null,
        ]);
    }

    public function show(User $user, Document $document)
    {
        // Verify document belongs to the user
        if ($document->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $documents = Document::where('user_id', $user->id)
            ->with('messages')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.chats.index', [
            'user' => $user,
            'documents' => $documents,
            'selectedDocument' => $document,
        ]);
    }
}
