<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class DocumentMessageController extends Controller
{
    public function chat(Document $document)
    {
        $this->authorizeAccess($document);
        return view('document.chat', ['document' => $document]);
    }

    public function index(Document $document)
    {
        $this->authorizeAccess($document);
        $messages = $document->messages()
            ->with('user:id,name,email')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, Document $document)
    {
        $this->authorizeAccess($document);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = $document->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        // Notify admin if message is from client
        if ($request->user()->isClient()) {
            $admins = User::where('role', 'admin')->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\ChatMessageNotification(
                    $message,
                    $document,
                    $request->user()
                ));
            }
        }

        // Notify client if message is from admin
        if ($request->user()->isAdmin()) {
            $document->user->notify(new \App\Notifications\ChatMessageNotification(
                $message,
                $document,
                $request->user()
            ));
        }

        return response()->json([
            'success' => true,
            'message' => $message->load('user:id,name,email'),
        ], 201);
    }

    private function authorizeAccess(Document $document): void
    {
        $user = auth()->user();

        if ($user->id !== $document->user_id && !$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }
    }
}
