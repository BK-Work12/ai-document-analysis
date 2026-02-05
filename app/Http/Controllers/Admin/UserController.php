<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,client',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'User created successfully!']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|' . Rule::unique('users')->ignore($user->id),
            'role' => 'required|in:admin,client',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'User updated successfully!']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 400);
            }
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    public function getUser(User $user)
    {
        if (request()->wantsJson()) {
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at->format('Y-m-d H:i'),
            ]);
        }

        return response()->json(['error' => 'Not Found'], 404);
    }

    /**
     * Show user's documents with message counts
     * GET /admin/users/{user}/documents
     */
    public function documents(User $user)
    {
        $documents = $user->documents()
            ->withCount('messages')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.documents', compact('user', 'documents'));
    }

    /**
     * Get document messages (AJAX)
     * GET /admin/users/documents/{document}/messages
     */
    public function documentMessages(\App\Models\Document $document)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $messages = $document->messages()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'document' => [
                'id' => $document->id,
                'filename' => $document->original_filename,
                'type' => $document->doc_type,
                'user' => $document->user->name,
            ],
            'messages' => $messages->map(fn($msg) => [
                'id' => $msg->id,
                'user_id' => $msg->user_id,
                'user_name' => $msg->user->name,
                'is_admin' => $msg->user->isAdmin(),
                'message' => $msg->message,
                'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    /**
     * Send message to document thread
     * POST /admin/users/documents/{document}/messages
     */
    public function sendDocumentMessage(\App\Models\Document $document, Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string|min:1|max:5000',
        ]);

        $message = $document->messages()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        $document->user->notify(new \App\Notifications\ChatMessageNotification(
            $message,
            $document,
            auth()->user()
        ));

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'user_id' => $message->user_id,
                'user_name' => auth()->user()->name,
                'is_admin' => true,
                'message' => $message->message,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
