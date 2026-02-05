<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AI\ClaudeChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        protected ClaudeChatService $claudeService
    ) {}

    /**
     * Send a message to the AI and get a response based on uploaded documents
     * This uses AWS Bedrock RAG (Retrieval-Augmented Generation)
     * 
     * Endpoint: POST /api/chat
     * Payload: { "message": "What does the ID document say about..." }
     * Response: { "response": "...", "sources": [...] }
     * 
     * Implementation Status: Days 13-14
     */
    public function chat(Request $request)
    {
        $user = auth()->user();
        
        // Validate request
        $request->validate([
            'message' => 'required|string|min:3|max:2000',
        ]);

        // TODO: Get user's Knowledge Base ID from database
        // $knowledgeBaseId = $user->knowledge_base_id;
        // if (!$knowledgeBaseId) {
        //     return response()->json(['error' => 'Documents not yet processed'], 403);
        // }

        // For now, return placeholder
        return response()->json([
            'message' => 'AI Chat API coming soon (Day 13-14)',
            'status' => 'pending',
            'feature' => 'AWS Bedrock RAG Integration',
            'implementation_timeline' => [
                'Days 11-12' => 'AWS Bedrock Knowledge Base setup + document embedding',
                'Days 13-14' => 'Chat API + Claude integration',
                'Day 15' => 'Conversation history + UI',
            ],
        ]);
    }

    /**
     * Stream chat response in real-time
     * 
     * Uses Server-Sent Events (SSE) for live updates
     */
    public function streamChat(Request $request)
    {
        return response()->stream(function () {
            // TODO: Implement streaming response
            // yield "data: Starting...\n\n";
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'Connection' => 'keep-alive',
        ]);
    }

    /**
     * Get chat history for the authenticated user
     * 
     * Endpoint: GET /api/chat/history
     * Response: [ { id, message, response, created_at }, ... ]
     */
    public function history()
    {
        // TODO: Retrieve conversation history from database
        // $conversations = auth()->user()->conversations()
        //     ->orderByDesc('created_at')
        //     ->paginate(20);

        return response()->json([
            'conversations' => [],
            'status' => 'pending',
            'message' => 'Chat history coming in Day 15',
        ]);
    }

    /**
     * Delete a conversation
     * 
     * Endpoint: DELETE /api/chat/{conversationId}
     */
    public function deleteConversation($conversationId)
    {
        // TODO: Delete conversation and related messages
        return response()->json(['deleted' => true]);
    }

    /**
     * Get sources/documents used for a specific response
     * 
     * Helpful for transparency - shows which documents were used
     * to generate the AI answer
     */
    public function getSources($conversationId)
    {
        // TODO: Retrieve sources from conversation
        return response()->json(['sources' => []]);
    }
}
