<?php

namespace App\Services\AI;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use Aws\Exception\AwsException;
use Exception;
use Illuminate\Support\Str;

/**
 * Document Analysis Service
 * 
 * Handles AI-powered document analysis using AWS Bedrock
 * Analyzes extracted text against the business loan requirements
 * Produces structured Case Findings Report
 * 
 * Features:
 * - Document classification against required file list
 * - Financial data extraction and validation
 * - Risk flag identification
 * - Cross-document consistency checking
 * - Confidence scoring
 */
class DocumentAnalysisService
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
        
        // Use Claude 3 Sonnet for better performance on document analysis
        $this->modelId = env('BEDROCK_MODEL_ID', 'anthropic.claude-3-sonnet-20240229-v1:0');
    }

    /**
     * Analyze extracted document text
     * 
     * @param string $extractedText Raw text from TextExtract
     * @param string $originalFilename Original document filename
     * @param string $docType Document type from upload
     * @return array Analysis result with classification, extracts, and risk flags
     */
    public function analyzeDocument(
        string $extractedText,
        string $originalFilename,
        string $docType
    ): array {
        try {
            // Build analysis prompt based on document type
            $prompt = $this->buildAnalysisPrompt($extractedText, $originalFilename, $docType);

            // Call Bedrock API
            $response = $this->callBedrock($prompt);

            if (!$response['success']) {
                return [
                    'success' => false,
                    'error' => $response['error'],
                ];
            }

            // Parse the response into structured format
            $analysisResult = $this->parseAnalysisResponse($response['content']);

            return [
                'success' => true,
                'analysis_result' => $analysisResult,
                'metadata' => [
                    'model' => $this->modelId,
                    'input_tokens' => $response['input_tokens'] ?? 0,
                    'output_tokens' => $response['output_tokens'] ?? 0,
                    'stop_reason' => $response['stop_reason'] ?? 'end_turn',
                ],
                'confidence_score' => $analysisResult['confidence_score'] ?? 0,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Analyze document directly from image bytes using Claude vision.
     */
    public function analyzeDocumentFromImage(
        string $imageBytes,
        string $imageMime,
        string $originalFilename,
        string $docType
    ): array {
        try {
            $prompt = $this->buildVisionAnalysisPrompt($originalFilename, $docType);
            $response = $this->callBedrockWithImage($prompt, $imageBytes, $imageMime);

            if (!$response['success']) {
                return [
                    'success' => false,
                    'error' => $response['error'],
                ];
            }

            $analysisResult = $this->parseAnalysisResponse($response['content']);

            return [
                'success' => true,
                'analysis_result' => $analysisResult,
                'metadata' => [
                    'model' => $this->modelId,
                    'analysis_mode' => 'direct_image',
                    'input_tokens' => $response['input_tokens'] ?? 0,
                    'output_tokens' => $response['output_tokens'] ?? 0,
                    'stop_reason' => $response['stop_reason'] ?? 'end_turn',
                ],
                'confidence_score' => $analysisResult['confidence_score'] ?? 0,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Perform cross-document validation and generate case findings report
     * 
     * @param array $documentsAnalysis Array of document analyses
     * @param array $metadata Additional metadata (company name, year, etc)
     * @return array Case findings report
     */
    public function generateCaseFindings(
        array $documentsAnalysis,
        array $metadata = []
    ): array {
        try {
            $prompt = $this->buildCaseFindingsPrompt($documentsAnalysis, $metadata);

            $response = $this->callBedrock($prompt);

            if (!$response['success']) {
                return [
                    'success' => false,
                    'error' => $response['error'],
                ];
            }

            $findings = $this->parseCaseFindingsResponse($response['content']);

            return [
                'success' => true,
                'findings' => $findings,
                'metadata' => [
                    'generated_at' => now()->toIso8601String(),
                    'document_count' => count($documentsAnalysis),
                    'tokens_used' => ($response['input_tokens'] ?? 0) + ($response['output_tokens'] ?? 0),
                ],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build analysis prompt for document
     * 
     * @param string $extractedText
     * @param string $filename
     * @param string $docType
     * @return string
     */
    protected function buildAnalysisPrompt(
        string $extractedText,
        string $filename,
        string $docType
    ): string {
        $systemPrompt = $this->getSystemPrompt();

        return <<<PROMPT
{$systemPrompt}

DOCUMENT TO ANALYZE:
Filename: {$filename}
Uploaded As: {$docType}

TEXT CONTENT:
{$extractedText}

Please analyze this document according to the system prompt above. Provide:
1. Document Classification - which required document type this actually is
2. Key Data Extracted - relevant financial, legal, or personal information
3. Validation Checks - verification of data accuracy and completeness
4. Risk Flags - any red flags, inconsistencies, or concerns
5. Confidence Score - how confident (0-100) you are in this analysis

Format your response as structured JSON.
PROMPT;
    }

    protected function buildVisionAnalysisPrompt(string $filename, string $docType): string
    {
        $systemPrompt = $this->getSystemPrompt();

        return <<<PROMPT
{$systemPrompt}

DOCUMENT TO ANALYZE:
Filename: {$filename}
Uploaded As: {$docType}

Analyze the attached image directly. Read all visible text from the image and then provide:
1. Document Classification - which required document type this actually is
2. Key Data Extracted - relevant financial, legal, or personal information
3. Validation Checks - verification of data accuracy and completeness
4. Risk Flags - any red flags, inconsistencies, or concerns
5. Confidence Score - how confident (0-100) you are in this analysis

Format your response as structured JSON.
PROMPT;
    }

    /**
     * Build prompt for cross-document case findings
     * 
     * @param array $documentsAnalysis
     * @param array $metadata
     * @return string
     */
    protected function buildCaseFindingsPrompt(array $documentsAnalysis, array $metadata): string
    {
        $systemPrompt = $this->getSystemPrompt();
        $documentsJson = json_encode($documentsAnalysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $metadataJson = json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return <<<PROMPT
{$systemPrompt}

ANALYZED DOCUMENTS:
{$documentsJson}

CASE METADATA:
{$metadataJson}

Based on the analyzed documents above, generate a comprehensive Case Findings Report including:

1. Financial Summary
   - Revenue trend
   - Profit trend  
   - Liquidity assessment
   - Debt burden analysis
   - Net worth calculation

2. QA & Integrity Report
   - Calculation errors found
   - Missing required documents
   - Cross-document inconsistencies

3. Risk Flags
   - Negative equity indicators
   - Overdraft/NSF activity
   - Tax arrears
   - Revenue decline
   - High fixed costs
   - Low credit scores
   - Unusual transactions

4. Decision Readiness
   - Case completeness score (0-100)
   - Data reliability score (0-100)
   - Human review required (Yes/No)

Format your response as structured JSON with clear sections.
PROMPT;
    }

    /**
     * Call Bedrock API with prompt
     * 
     * @param string $prompt
     * @return array Response data
     */
    protected function callBedrock(string $prompt): array
    {
        try {
            $systemPrompt = "You are an expert AI Financial Case Analyst. Provide structured, fact-based analysis without fabrication.";

            if (str_starts_with($this->modelId, 'arn:aws:bedrock:')) {
                $result = $this->bedrockClient->converse([
                    'modelId' => $this->modelId,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
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

            $messages = [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ];

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
                'error' => $e->getAwsErrorMessage() ?? $e->getMessage(),
                'error_code' => $e->getAwsErrorCode() ?? 'BEDROCK_ERROR',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function callBedrockWithImage(string $prompt, string $imageBytes, string $imageMime): array
    {
        try {
            $format = $this->mapMimeToBedrockImageFormat($imageMime);

            if ($format === null) {
                return [
                    'success' => false,
                    'error' => "Unsupported image MIME type for Claude vision: {$imageMime}",
                ];
            }

            $systemPrompt = "You are an expert AI Financial Case Analyst. Provide structured, fact-based analysis without fabrication.";

            $result = $this->bedrockClient->converse([
                'modelId' => $this->modelId,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['text' => $prompt],
                            [
                                'image' => [
                                    'format' => $format,
                                    'source' => [
                                        'bytes' => $imageBytes,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
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
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getAwsErrorMessage() ?? $e->getMessage(),
                'error_code' => $e->getAwsErrorCode() ?? 'BEDROCK_ERROR',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function mapMimeToBedrockImageFormat(string $mime): ?string
    {
        return match (strtolower(trim($mime))) {
            'image/jpeg', 'image/jpg' => 'jpeg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => null,
        };
    }

    /**
     * Parse analysis response into structured format
     * 
     * @param string $response
     * @return array
     */
    protected function parseAnalysisResponse(string $response): array
    {
        // Try to extract JSON from response
        $jsonMatch = preg_match('/\{[\s\S]*\}/', $response, $matches);
        
        if ($jsonMatch && isset($matches[0])) {
            $parsed = json_decode($matches[0], true);
            if ($parsed) {
                return [
                    'classification' => $parsed['classification'] ?? null,
                    'extracted_data' => $parsed['extracted_data'] ?? [],
                    'validation_checks' => $parsed['validation_checks'] ?? [],
                    'risk_flags' => $parsed['risk_flags'] ?? [],
                    'missing_fields' => $parsed['missing_fields'] ?? [],
                    'confidence_score' => (float)($parsed['confidence_score'] ?? 0),
                ];
            }
        }

        // Fallback: return raw response in structured format
        return [
            'classification' => null,
            'extracted_data' => [],
            'validation_checks' => [],
            'risk_flags' => ['AI analysis output was not valid JSON. Manual review required.'],
            'missing_fields' => [],
            'raw_analysis' => $response,
            'confidence_score' => 50, // Low confidence if we couldn't parse
        ];
    }

    /**
     * Parse case findings response
     * 
     * @param string $response
     * @return array
     */
    protected function parseCaseFindingsResponse(string $response): array
    {
        $jsonMatch = preg_match('/\{[\s\S]*\}/', $response, $matches);
        
        if ($jsonMatch && isset($matches[0])) {
            $parsed = json_decode($matches[0], true);
            if ($parsed) {
                return $parsed;
            }
        }

        return [
            'raw_findings' => $response,
            'decision_readiness' => [
                'case_completeness_score' => 0,
                'data_reliability_score' => 0,
                'human_review_required' => true,
            ],
        ];
    }

    /**
     * Get the system prompt for document analysis
     * 
     * @return string
     */
    protected function getSystemPrompt(): string
    {
        return <<<'PROMPT'
🧠 AI BUSINESS LOAN CASE ANALYST & QA AUDITOR
You are an AI Financial Case Analyst processing business loan applications.
Your responsibilities are to:
- Classify each uploaded document according to the required file list
- Extract key financial, legal, and identity data
- Verify mathematical accuracy and internal consistency
- Cross-check data across documents
- Identify discrepancies, anomalies, and risk indicators
- Generate clear financial summaries and analytical insights
- Produce a structured Case Findings Report

⚠️ OPERATING RULES
- Never assume or fabricate values
- Only use information found in the documents
- Cite which document supports each finding
- Separate facts from analysis
- Flag uncertainty clearly
- Prefer conservative interpretation
- Highlight discrepancies explicitly

📁 REQUIRED COMPANY DOCUMENTS
1. Financial Statements (Year 1, 2, 3)
2. Interim Financial Statements
3. Bank Statements (Year 1+ interim months)
4. Articles of Incorporation
5. Certificate of Incorporation
6. Shareholder Registry
7. Notice of Assessment (CRA Screenshot)
8. T2 Corporate Tax Return (if NoA unavailable)
9. Lease Agreements

📁 REQUIRED PERSONAL DOCUMENTS
1. Personal Statement of Affairs (PSOA)
2. Driver's License (identity validation)
3. Credit Score Screenshot

🔁 GLOBAL CROSS-DOCUMENT QA RULES
- Financial statement revenue ≈ bank statement deposits
- Cash on Balance Sheet = bank closing balance
- Shareholder names match Driver's License
- Tax documents match financial statements
- Lease payments appear in expenses
- No missing required documents
- No unexplained discrepancies
PROMPT;
    }
}
