<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUserConversation;
use App\Models\User;
use App\Services\AI\AdminUserDocumentChatService;
use Illuminate\Http\Request;

class UserDocumentChatController extends Controller
{
    public function __construct(protected AdminUserDocumentChatService $chatService) {}

    public function index()
    {
        $users = User::where('role', 'client')
            ->withCount('documents')
            ->having('documents_count', '>', 0)
            ->orderBy('name')
            ->get();

        return view('admin.documents.user-conversations', compact('users'));
    }

    public function conversation(User $user)
    {
        if (!$user->isClient()) {
            return response()->json([
                'success' => false,
                'error' => 'User must be a client',
            ], 422);
        }

        $conversation = $this->chatService->getOrCreateConversation($user, auth()->id());

        $messages = $conversation->messages()->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'status' => $conversation->status,
                'messages' => $messages->map(fn($msg) => [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                ]),
            ],
        ]);
    }

    public function sendMessage(AdminUserConversation $conversation, Request $request)
    {
        if ($conversation->admin_user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized conversation access',
            ], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|min:1|max:5000',
        ]);

        $result = $this->chatService->sendMessage(
            $conversation,
            $validated['message'],
            auth()->id()
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $result['message']->id,
                'sender_type' => 'ai',
                'message' => $result['content'],
                'metadata' => $result['metadata'],
                'sent_at' => $result['message']->sent_at,
            ],
        ], 201);
    }
}
