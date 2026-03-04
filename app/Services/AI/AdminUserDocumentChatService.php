<?php

namespace App\Services\AI;

use App\Models\AdminUserChatMessage;
use App\Models\AdminUserConversation;
use App\Models\User;
use App\Services\ApplicationAuditLogger;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use Aws\Exception\AwsException;
use Exception;

class AdminUserDocumentChatService
{
    protected BedrockRuntimeClient $bedrockClient;
    protected string $modelId;
    protected int $maxTokens = 4096;

    public function __construct()
    {
        $this->bedrockClient = new BedrockRuntimeClient([
            'region' => env('BEDROCK_REGION', env('AWS_DEFAULT_REGION', 'us-east-2')),
            'version' => 'latest',
        ]);

        $this->modelId = env('BEDROCK_MODEL_ID', 'anthropic.claude-3-sonnet-20240229-v1:0');
    }

    public function getOrCreateConversation(User $clientUser, int $adminUserId): AdminUserConversation
    {
        $conversation = AdminUserConversation::firstOrCreate(
            [
                'client_user_id' => $clientUser->id,
                'admin_user_id' => $adminUserId,
            ],
            [
                'title' => 'All Documents Conversation',
                'context' => $this->buildUserDocumentContext($clientUser),
                'last_message_at' => now(),
            ]
        );

        $conversation->update([
            'context' => $this->buildUserDocumentContext($clientUser),
        ]);

        return $conversation;
    }

    public function sendMessage(AdminUserConversation $conversation, string $userMessage, int $adminUserId): array
    {
        try {
            $conversation->load('clientUser');
            if ($conversation->clientUser) {
                $conversation->update([
                    'context' => $this->buildUserDocumentContext($conversation->clientUser),
                ]);
                $conversation->refresh();
            }

            AdminUserChatMessage::create([
                'admin_user_conversation_id' => $conversation->id,
                'sender_type' => 'user',
                'user_id' => $adminUserId,
                'message' => $userMessage,
                'role' => 'user',
                'sent_at' => now(),
            ]);

            app(ApplicationAuditLogger::class)->log(
                actionType: 'ai.query.user_document_chat',
                userId: $adminUserId,
                entityType: 'admin_user_conversation',
                entityId: $conversation->id,
                description: 'Admin queried AI for user-wide document chat.',
                metadata: [
                    'client_user_id' => $conversation->client_user_id,
                    'message_length' => mb_strlen($userMessage),
                ]
            );

            $messages = $this->buildMessageHistory($conversation);
            $systemPrompt = $this->buildSystemPrompt($conversation->context ?? 'No context available.');
            $response = $this->callBedrockChat($messages, $systemPrompt);

            if (!$response['success']) {
                return [
                    'success' => false,
                    'error' => $response['error'],
                ];
            }

            $responseContent = trim((string) ($response['content'] ?? ''));
            $totalInputTokens = (int) ($response['input_tokens'] ?? 0);
            $totalOutputTokens = (int) ($response['output_tokens'] ?? 0);
            $stopReason = (string) ($response['stop_reason'] ?? 'end_turn');
            $continuationAttempts = 0;
            $maxContinuationAttempts = max(2, (int) env('BEDROCK_CHAT_MAX_CONTINUATIONS', 12));
            $latestAssistantChunk = $responseContent;

            while ($stopReason === 'max_tokens' && $continuationAttempts < $maxContinuationAttempts) {
                $continuationAttempts++;

                if ($latestAssistantChunk !== '') {
                    $messages[] = [
                        'role' => 'assistant',
                        'content' => $latestAssistantChunk,
                    ];
                }

                $messages[] = [
                    'role' => 'user',
                    'content' => 'Continue exactly from where you stopped. Return only the continuation and do not repeat earlier text.',
                ];

                $continuation = $this->callBedrockChat($messages, $systemPrompt);
                if (!$continuation['success']) {
                    break;
                }

                $continuationText = trim((string) ($continuation['content'] ?? ''));
                if ($continuationText === '') {
                    break;
                }

                $responseContent = trim($responseContent . "\n\n" . $continuationText);
                $latestAssistantChunk = $continuationText;
                $totalInputTokens += (int) ($continuation['input_tokens'] ?? 0);
                $totalOutputTokens += (int) ($continuation['output_tokens'] ?? 0);
                $stopReason = (string) ($continuation['stop_reason'] ?? 'end_turn');
            }

            if ($stopReason === 'max_tokens') {
                $responseContent = rtrim($responseContent) . "\n\n[Response reached model length limit after auto-continuation. Ask 'continue' to keep going.]";
            }

            if ($responseContent === '') {
                return [
                    'success' => false,
                    'error' => 'AI returned an empty response. Please try again.',
                ];
            }

            $aiMessage = AdminUserChatMessage::create([
                'admin_user_conversation_id' => $conversation->id,
                'sender_type' => 'ai',
                'message' => $responseContent,
                'role' => 'assistant',
                'metadata' => [
                    'model' => $this->modelId,
                    'input_tokens' => $totalInputTokens,
                    'output_tokens' => $totalOutputTokens,
                    'stop_reason' => $stopReason,
                    'continuation_attempts' => $continuationAttempts,
                ],
                'sent_at' => now(),
            ]);

            $conversation->update(['last_message_at' => now()]);

            return [
                'success' => true,
                'message' => $aiMessage,
                'content' => $responseContent,
                'metadata' => $aiMessage->metadata,
            ];
        } catch (Exception $e) {
            \Log::error('AdminUserDocumentChatService.sendMessage failed', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function buildMessageHistory(AdminUserConversation $conversation): array
    {
        $messages = [];

        $chatMessages = $conversation->messages()
            ->orderBy('created_at')
            ->limit(12)
            ->get();

        foreach ($chatMessages as $msg) {
            $messages[] = [
                'role' => $msg->role,
                'content' => $msg->message,
            ];
        }

        return $messages;
    }

    protected function buildUserDocumentContext(User $clientUser): string
    {
        $documents = $clientUser->documents()
            ->orderByDesc('uploaded_at')
            ->orderByDesc('created_at')
            ->get();

        $header = "CLIENT PROFILE\n";
        $header .= "Client ID: {$clientUser->id}\n";
        $header .= "Client Name: {$clientUser->name}\n";
        $header .= "Client Email: {$clientUser->email}\n";
        $header .= "Documents: {$documents->count()}\n\n";

        if ($documents->isEmpty()) {
            return $header . "No uploaded documents available for this user.";
        }

        $body = "DOCUMENT PORTFOLIO CONTEXT\n";

        foreach ($documents as $index => $document) {
            $docNumber = $index + 1;
            $analysisResult = is_string($document->analysis_result)
                ? $document->analysis_result
                : json_encode($document->analysis_result);
            if (!$analysisResult) {
                $analysisResult = 'Not available yet';
            }

            $extractedText = is_string($document->extracted_text) ? $document->extracted_text : '';
            $extractedPreview = mb_substr($extractedText, 0, 4000);
            if ($extractedPreview === '') {
                $extractedPreview = 'Not extracted yet';
            }

            $classifiedType = $document->classified_doc_type ?: 'Not classified yet';
            $analysisStatus = $document->analysis_status ?: 'pending';
            $riskFlags = $document->risk_flags ? json_encode($document->risk_flags) : '[]';
            $missingFields = $document->missing_fields ? json_encode($document->missing_fields) : '[]';

            $body .= "\n--- DOCUMENT {$docNumber} ---\n";
            $body .= "Document ID: {$document->id}\n";
            $body .= "Filename: {$document->original_filename}\n";
            $body .= "Uploaded Type: {$document->doc_type}\n";
            $body .= "Classified Type: {$classifiedType}\n";
            $body .= "Status: {$document->status}\n";
            $body .= "Analysis Status: {$analysisStatus}\n";
            $body .= "Confidence: {$document->confidence_score}%\n";
            $body .= "Risk Flags: {$riskFlags}\n";
            $body .= "Missing Fields: {$missingFields}\n";
            $body .= "Analysis Result: {$analysisResult}\n";
            $body .= "Extracted Text Preview: {$extractedPreview}\n";
        }

        return $header . $body;
    }

    protected function buildSystemPrompt(string $context): string
    {
        $currentDate = now()->toDateString();

        return <<<PROMPT
You are an expert AI Financial Analyst assistant helping an admin review a single client's full document set.
The current date is {$currentDate}. Do not treat recent dates (like 2025 or 2026) as future-dated.

You have access to:
1. All uploaded documents for this client
2. Document extracted text previews
3. AI analysis outputs, risk flags, and missing fields (when available)
4. Multi-turn chat history

Your role:
- Answer cross-document questions for this client
- Compare consistency across documents
- Highlight missing/inconsistent data points
- Explain risks clearly and practically
- Suggest concrete follow-up actions for the admin

Important:
- Use only the provided context and chat history
- Do not invent values
- If info is missing, say so directly
- Reference specific document IDs/filenames when possible
PROMPT . "\n\n" . $context;
    }

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

                $outputBlocks = $result['output']['message']['content'] ?? [];
                $content = $this->extractTextFromContentBlocks(is_array($outputBlocks) ? $outputBlocks : []);

                if ($content === '') {
                    return [
                        'success' => false,
                        'error' => 'AI returned an empty response block.',
                    ];
                }

                return [
                    'success' => true,
                    'content' => $content,
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

            $response = json_decode((string) $result['body'], true);

            $contentBlocks = $response['content'] ?? [];
            $content = $this->extractTextFromContentBlocks(is_array($contentBlocks) ? $contentBlocks : []);

            if ($content === '') {
                return [
                    'success' => false,
                    'error' => 'AI returned an empty response block.',
                ];
            }

            return [
                'success' => true,
                'content' => $content,
                'input_tokens' => $response['usage']['input_tokens'] ?? 0,
                'output_tokens' => $response['usage']['output_tokens'] ?? 0,
                'stop_reason' => $response['stop_reason'] ?? 'end_turn',
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getAwsErrorCode() ?? 'BEDROCK_ERROR',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract and join all text blocks returned by Bedrock/Claude.
     *
     * @param array<int, mixed> $blocks
     */
    protected function extractTextFromContentBlocks(array $blocks): string
    {
        $parts = [];

        foreach ($blocks as $block) {
            if (is_array($block) && is_string($block['text'] ?? null)) {
                $text = trim($block['text']);
                if ($text !== '') {
                    $parts[] = $text;
                }
            }
        }

        return trim(implode("\n\n", $parts));
    }
}
