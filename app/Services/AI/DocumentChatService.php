<?php

namespace App\Services\AI;

use App\Models\DocumentConversation;
use App\Models\DocumentChatMessage;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use Aws\Exception\AwsException;
use Exception;

/**
 * Document Chat Service
 * 
 * Handles admin chat conversations about specific documents
 * Uses extracted document text as context for Bedrock Claude
 * 
 * Features:
 * - Multi-turn conversations with context awareness
 * - Document text as RAG context
 * - Token tracking and cost calculation
 * - Message history management
 */
class DocumentChatService
{
    protected BedrockRuntimeClient $bedrockClient;
    protected string $modelId;
    protected int $maxTokens = 2048;

    public function __construct()
    {
        $this->bedrockClient = new BedrockRuntimeClient([
            'region' => env('BEDROCK_REGION', env('AWS_DEFAULT_REGION', 'us-east-2')),
            'version' => 'latest',
        ]);
        
        $this->modelId = env('BEDROCK_MODEL_ID', 'anthropic.claude-3-sonnet-20240229-v1:0');
    }

    /**
     * Send message in document conversation
     * 
     * @param DocumentConversation $conversation
     * @param string $userMessage
     * @param int $userId
     * @return array Response with AI message
     */
    public function sendMessage(
        DocumentConversation $conversation,
        string $userMessage,
        int $userId
    ): array {
        try {
            // Store user message
            DocumentChatMessage::create([
                'document_conversation_id' => $conversation->id,
                'sender_type' => 'user',
                'user_id' => $userId,
                'message' => $userMessage,
                'role' => 'user',
                'sent_at' => now(),
            ]);

            // Build conversation history
            $messages = $this->buildMessageHistory($conversation);

            // Get document context
            $documentContext = $this->buildDocumentContext($conversation->document);

            // Build system prompt
            $systemPrompt = $this->buildChatSystemPrompt($documentContext);

            // Call Bedrock
            $response = $this->callBedrockChat($messages, $systemPrompt);

            if (!$response['success']) {
                return [
                    'success' => false,
                    'error' => $response['error'],
                ];
            }

            // Store AI message
            $aiMessage = DocumentChatMessage::create([
                'document_conversation_id' => $conversation->id,
                'sender_type' => 'ai',
                'message' => $response['content'],
                'role' => 'assistant',
                'metadata' => [
                    'model' => $this->modelId,
                    'input_tokens' => $response['input_tokens'] ?? 0,
                    'output_tokens' => $response['output_tokens'] ?? 0,
                    'stop_reason' => $response['stop_reason'] ?? 'end_turn',
                ],
                'sent_at' => now(),
            ]);

            // Update conversation timestamp
            $conversation->update(['last_message_at' => now()]);

            return [
                'success' => true,
                'message' => $aiMessage,
                'content' => $response['content'],
                'metadata' => $aiMessage->metadata,
            ];

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('DocumentChatService.sendMessage failed', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build conversation message history for Bedrock
     * 
     * @param DocumentConversation $conversation
     * @return array Messages array for Bedrock format
     */
    protected function buildMessageHistory(DocumentConversation $conversation): array
    {
        $messages = [];
        
        // Get last 10 messages for context (avoid token limit)
        $chatMessages = $conversation->messages()
            ->orderBy('created_at')
            ->limit(10)
            ->get();

        foreach ($chatMessages as $msg) {
            $messages[] = [
                'role' => $msg->role,
                'content' => $msg->message,
            ];
        }

        return $messages;
    }

    /**
     * Build document context for the conversation
     * 
     * @param $document
     * @return string
     */
    protected function buildDocumentContext($document): string
    {
        $context = <<<CONTEXT
DOCUMENT CONTEXT:
Document ID: {$document->id}
Document Type: {$document->doc_type}
Filename: {$document->original_filename}
Classification: {$document->classified_doc_type}
Confidence Score: {$document->confidence_score}%

EXTRACTED TEXT:
{$document->extracted_text}

ANALYSIS RESULTS:
{$document->analysis_result}

TAGS:
CONTEXT;

        $tags = $document->tags->pluck('name')->join(', ');
        if ($tags) {
            $context .= "\n" . $tags;
        }

        $context .= "\n\nFLAGS:\n";
        $flags = json_decode($document->risk_flags, true);
        if ($flags) {
            foreach ($flags as $flag) {
                $context .= "- " . (is_array($flag) ? ($flag['description'] ?? $flag['message'] ?? '') : $flag) . "\n";
            }
        }

        return $context;
    }

    /**
     * Build system prompt for document chat
     * 
     * @param string $documentContext
     * @return string
     */
    protected function buildChatSystemPrompt(string $documentContext): string
    {
        return <<<'PROMPT'
You are an expert AI Financial Analyst assistant helping an admin review a business loan application document.

You have access to:
1. The extracted text from the document
2. AI analysis results (classification, risk flags, extracted data)
3. Tags applied to the document
4. Previous conversation history

Your role is to:
- Answer questions about the document
- Provide detailed explanations of findings
- Highlight relevant sections when asked
- Identify inconsistencies or issues
- Suggest additional information to request
- Explain risk flags and their implications
- Help verify data accuracy

IMPORTANT:
- Always cite specific sections or data from the document
- Never fabricate information
- Flag if information is missing or unclear
- Be precise with financial figures and dates
- Help the admin make informed decisions

PROMPT . "\n\n" . $documentContext;
    }

    /**
     * Call Bedrock with chat messages
     * 
     * @param array $messages
     * @param string $systemPrompt
     * @return array
     */
    protected function callBedrockChat(array $messages, string $systemPrompt): array
    {
        try {
            if (str_starts_with($this->modelId, 'arn:aws:bedrock:')) {
                $converseMessages = array_map(function ($message) {
                    $content = $message['content'];
                    if (is_string($content)) {
                        $content = [['text' => $content]];
                    }

                    return [
                        'role' => $message['role'],
                        'content' => $content,
                    ];
                }, $messages);

                $result = $this->bedrockClient->converse([
                    'modelId' => $this->modelId,
                    'messages' => $converseMessages,
                    'system' => [
                        ['text' => $systemPrompt],
                    ],
                    'inferenceConfig' => [
                        'maxTokens' => $this->maxTokens,
                    ],
                ]);

                return [
                    'success' => true,
                    'content' => $result['output']['message']['content'][0]['text'] ?? '',
                    'input_tokens' => $result['usage']['inputTokens'] ?? 0,
                    'output_tokens' => $result['usage']['outputTokens'] ?? 0,
                    'stop_reason' => $result['stopReason'] ?? 'end_turn',
                ];
            }

            $result = $this->bedrockClient->invokeModel([
                'modelId' => $this->modelId,
                'contentType' => 'application/json',
                'accept' => 'application/json',
                'body' => json_encode([
                    'anthropic_version' => 'bedrock-2023-06-01',
                    'max_tokens' => $this->maxTokens,
                    'messages' => $messages,
                    'system' => $systemPrompt,
                ]),
            ]);

            $response = json_decode((string)$result['body'], true);

            return [
                'success' => true,
                'content' => $response['content'][0]['text'] ?? '',
                'input_tokens' => $response['usage']['input_tokens'] ?? 0,
                'output_tokens' => $response['usage']['output_tokens'] ?? 0,
                'stop_reason' => $response['stop_reason'] ?? 'end_turn',
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getAwsErrorDetails()['Code'] ?? 'BEDROCK_ERROR',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a new conversation for a document
     * 
     * @param $document
     * @param int $userId
     * @param string $title
     * @return DocumentConversation
     */
    public function createConversation($document, int $userId, string $title = null): DocumentConversation
    {
        $title = $title ?? "Discussion: " . $document->original_filename;

        return DocumentConversation::create([
            'document_id' => $document->id,
            'user_id' => $userId,
            'title' => $title,
            'context' => $this->buildDocumentContext($document),
            'last_message_at' => now(),
        ]);
    }

    /**
     * Get conversation summary
     * 
     * @param DocumentConversation $conversation
     * @return array
     */
    public function getConversationSummary(DocumentConversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'document_id' => $conversation->document_id,
            'title' => $conversation->title,
            'status' => $conversation->status,
            'message_count' => $conversation->messages()->count(),
            'last_message_at' => $conversation->last_message_at,
            'created_at' => $conversation->created_at,
            'messages' => $conversation->messages()->get(['id', 'sender_type', 'message', 'sent_at'])->toArray(),
        ];
    }
}
