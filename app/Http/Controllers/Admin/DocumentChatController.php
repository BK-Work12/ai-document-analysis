<?php

namespace App\Http\Controllers\Admin;

use App\Models\Document;
use App\Models\DocumentConversation;
use App\Services\AI\DocumentChatService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Document Chat Controller
 * 
 * Handles admin chat with Bedrock about specific documents
 * Endpoint for document-specific conversations
 */
class DocumentChatController extends Controller
{
    public function __construct(protected DocumentChatService $chatService) {}

    /**
     * Show document conversations page (HTML view)
     * GET /admin/documents/chat
     */
    public function index()
    {
        $users = \App\Models\User::withCount('documents')
            ->having('documents_count', '>', 0)
            ->orderBy('name')
            ->get();

        return view('admin.documents.conversations', compact('users'));
    }

    /**
     * Get user's documents
     * GET /admin/documents/chat/users/{user}/documents
     */
    public function userDocuments(\App\Models\User $user)
    {
        $documents = $user->documents()
            ->orderByDesc('uploaded_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'documents' => $documents->map(fn($doc) => [
                'id' => $doc->id,
                'filename' => $doc->original_filename,
                'type' => $doc->doc_type,
                'analysis_status' => $doc->analysis_status,
                'analyzed_at' => $doc->analysis_completed_at?->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    /**
     * Get or create single conversation for a document (JSON API)
     * GET /admin/documents/{document}/conversations
     */
    public function conversations(Document $document)
    {
        // Eager load relationships needed for conversation context
        $document->load(['tags', 'user']);
        
        // Get or create the single conversation for this document
        $conversation = $document->conversations()->first();
        
        if (!$conversation) {
            $conversation = $this->chatService->createConversation(
                $document,
                auth()->id(),
                'Document Analysis Chat'
            );
        }

        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'document' => [
                'id' => $document->id,
                'type' => $document->doc_type,
                'filename' => $document->original_filename,
                'classification' => $document->classified_doc_type,
                'user' => $document->user->name,
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

    /**
     * Start new conversation for a document
     * POST /admin/documents/{document}/conversations
     */
    public function startConversation(Document $document, Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
        ]);

        $conversation = $this->chatService->createConversation(
            $document,
            auth()->id(),
            $validated['title'] ?? null
        );

        return response()->json([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'status' => $conversation->status,
                'created_at' => $conversation->created_at,
            ],
        ], 201);
    }

    /**
     * Get conversation messages
     * GET /admin/conversations/{conversation}
     */
    public function show(DocumentConversation $conversation)
    {
        try {
            $this->authorizeConversation($conversation);

            $summary = $this->chatService->getConversationSummary($conversation);

            return response()->json([
                'success' => true,
                'conversation' => $summary,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading conversation', [
                'conversation_id' => $conversation->id ?? null,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send message in conversation
     * POST /admin/conversations/{conversation}/messages
     */
    public function sendMessage(DocumentConversation $conversation, Request $request)
    {
        try {
            $this->authorizeConversation($conversation);

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed: ' . implode(', ', $e->errors()['message'] ?? ['Invalid message']),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error sending message', [
                'conversation_id' => $conversation->id ?? null,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update conversation status
     * PATCH /admin/conversations/{conversation}
     */
    public function updateStatus(DocumentConversation $conversation, Request $request)
    {
        $this->authorizeConversation($conversation);

        $validated = $request->validate([
            'status' => 'required|in:active,archived,closed',
            'title' => 'nullable|string|max:255',
        ]);

        $conversation->update($validated);

        return response()->json([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'status' => $conversation->status,
                'updated_at' => $conversation->updated_at,
            ],
        ]);
    }

    /**
     * Delete conversation
     * DELETE /admin/conversations/{conversation}
     */
    public function destroy(DocumentConversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted',
        ]);
    }

    /**
     * Send AI message to client
     * POST /admin/conversations/{conversation}/messages/{message}/send-to-client
     */
    public function sendMessageToClient(DocumentConversation $conversation, \App\Models\DocumentChatMessage $message, Request $request)
    {
        try {
            $this->authorizeConversation($conversation);

            // Ensure message belongs to this conversation
            if ($message->document_conversation_id !== $conversation->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Message does not belong to this conversation',
                ], 403);
            }

            // Ensure message is from AI
            if ($message->sender_type !== 'ai') {
                return response()->json([
                    'success' => false,
                    'error' => 'Only AI messages can be sent to client',
                ], 400);
            }

            // Create a document message for the client
            $docMessage = $conversation->document->messages()->create([
                'user_id' => auth()->id(),
                'message' => $message->message,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent to client',
                'data' => $docMessage,
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error sending message to client', [
                'conversation_id' => $conversation->id ?? null,
                'message_id' => $message->id ?? null,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Edit chat message
     * PATCH /admin/conversations/{conversation}/messages/{message}
     */
    public function updateMessage(DocumentConversation $conversation, \App\Models\DocumentChatMessage $message, Request $request)
    {
        try {
            $this->authorizeConversation($conversation);

            // Ensure message belongs to this conversation
            if ($message->document_conversation_id !== $conversation->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Message does not belong to this conversation',
                ], 403);
            }

            $validated = $request->validate([
                'message' => 'required|string|min:1|max:5000',
            ]);

            // Only allow editing user messages
            if ($message->sender_type !== 'user') {
                return response()->json([
                    'success' => false,
                    'error' => 'Only user messages can be edited',
                ], 400);
            }

            $message->update([
                'message' => $validated['message'],
            ]);

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'sender_type' => $message->sender_type,
                    'message' => $message->message,
                    'updated_at' => $message->updated_at->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating message', [
                'conversation_id' => $conversation->id ?? null,
                'message_id' => $message->id ?? null,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Authorize user can access conversation
     * 
     * @param DocumentConversation $conversation
     * @return void
     */
    protected function authorizeConversation(DocumentConversation $conversation)
    {
        // Ensure user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Admin access required');
        }
        
        // Optionally check if conversation exists
        if (!$conversation->exists) {
            abort(404, 'Conversation not found');
        }
    }
}
