<?php

namespace App\Services\AI;

use Aws\Textract\TextractClient;
use Aws\Exception\AwsException;
use Exception;

/**
 * Text Extract Service
 * 
 * Handles text extraction from documents using AWS TextExtract
 * Supports PDF, Images (PNG, JPEG), and other document formats
 * 
 * Features:
 * - Async document processing
 * - Text extraction with confidence scores
 * - Metadata extraction (page count, detected fields)
 * - Error handling and retry logic
 */
class TextExtractService
{
    protected TextractClient $textractClient;

    public function __construct()
    {
        $this->textractClient = new TextractClient([
            'region' => config('filesystems.disks.s3.region') ?? env('AWS_DEFAULT_REGION', 'us-east-2'),
            'version' => 'latest',
        ]);
    }

    /**
     * Start async document analysis job
     * 
     * @param string $s3BucketName
     * @param string $s3ObjectKey
     * @param string|null $snsTopicArn SNN topic for notifications
     * @param string|null $roleArn IAM role ARN
     * @return array Job information with JobId
     */
    public function startDocumentAnalysis(
        string $s3BucketName,
        string $s3ObjectKey,
        ?string $snsTopicArn = null,
        ?string $roleArn = null
    ): array {
        try {
            $params = [
                'DocumentLocation' => [
                    'S3Object' => [
                        'Bucket' => $s3BucketName,
                        'Name' => $s3ObjectKey,
                    ],
                ],
            ];

            // Add SNS notification if provided
            if ($snsTopicArn && $roleArn) {
                $params['NotificationChannel'] = [
                    'SNSTopicArn' => $snsTopicArn,
                    'RoleArn' => $roleArn,
                ];
            }

            $result = $this->textractClient->startDocumentAnalysis($params);

            return [
                'success' => true,
                'job_id' => $result['JobId'],
                'status' => 'submitted',
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getAwsErrorMessage() ?? $e->getMessage(),
                'error_code' => $e->getAwsErrorCode() ?? 'UNKNOWN_ERROR',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Synchronous document text extraction from bytes (local storage)
     * Maximum file size: 5MB
     * 
     * @param string $bytes
     * @return array Job result with extracted text and metadata
     */
    public function detectDocumentTextBytes(string $bytes): array
    {
        try {
            $result = $this->textractClient->detectDocumentText([
                'Document' => [
                    'Bytes' => $bytes,
                ],
            ]);

            return [
                'success' => true,
                'extracted_text' => $this->extractTextFromBlocks($result['Blocks']),
                'metadata' => [
                    'pages' => $result->get('DocumentMetadata.Pages', 1),
                    'detected_lines' => $this->countBlocksByType($result['Blocks'], 'LINE'),
                    'detected_words' => $this->countBlocksByType($result['Blocks'], 'WORD'),
                ],
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getAwsErrorMessage() ?? $e->getMessage(),
                'error_code' => $e->getAwsErrorCode() ?? 'UNKNOWN_ERROR',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get results of async document analysis
     * 
     * @param string $jobId
     * @param int|null $maxResults
     * @param string|null $nextToken
     * @return array Job result with extracted text and metadata
     */
    public function getDocumentAnalysisResults(
        string $jobId,
        ?int $maxResults = null,
        ?string $nextToken = null
    ): array {
        try {
            $params = ['JobId' => $jobId];

            if ($maxResults) {
                $params['MaxResults'] = $maxResults;
            }
            if ($nextToken) {
                $params['NextToken'] = $nextToken;
            }

            $result = $this->textractClient->getDocumentAnalysis($params);

            return [
                'success' => true,
                'job_id' => $result['JobId'],
                'status' => $result['JobStatus'],
                'pages' => $result->get('DocumentMetadata.Pages', 0),
                'blocks' => $result->get('Blocks', []),
                'next_token' => $result->get('NextToken'),
                'warnings' => $result->get('Warnings', []),
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'status' => 'FAILED',
                'error' => $e->getAwsErrorMessage() ?? $e->getMessage(),
                'error_code' => $e->getAwsErrorCode() ?? 'UNKNOWN_ERROR',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'status' => 'FAILED',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Detect document text (synchronous, for small files)
     * Maximum file size: 5MB
     * 
     * @param string $s3BucketName
     * @param string $s3ObjectKey
     * @return array Extracted text and blocks
     */
    public function detectDocumentText(string $s3BucketName, string $s3ObjectKey): array
    {
        try {
            $result = $this->textractClient->detectDocumentText([
                'Document' => [
                    'S3Object' => [
                        'Bucket' => $s3BucketName,
                        'Name' => $s3ObjectKey,
                    ],
                ],
            ]);

            $extractedText = $this->extractTextFromBlocks($result['Blocks']);
            
            return [
                'success' => true,
                'extracted_text' => $extractedText,
                'blocks' => $result['Blocks'],
                'metadata' => [
                    'pages' => $result->get('DocumentMetadata.Pages', 1),
                    'detected_lines' => $this->countBlocksByType($result['Blocks'], 'LINE'),
                    'detected_words' => $this->countBlocksByType($result['Blocks'], 'WORD'),
                ],
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getAwsErrorMessage() ?? $e->getMessage(),
                'error_code' => $e->getAwsErrorCode() ?? 'UNKNOWN_ERROR',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract continuous text from blocks
     * 
     * @param array $blocks TextExtract blocks
     * @return string Extracted text
     */
    protected function extractTextFromBlocks(array $blocks): string
    {
        $textParts = [];

        foreach ($blocks as $block) {
            if ($block['BlockType'] === 'LINE' && isset($block['Text'])) {
                $textParts[] = $block['Text'];
            }
        }

        return implode("\n", $textParts);
    }

    /**
     * Count blocks of a specific type
     * 
     * @param array $blocks
     * @param string $blockType
     * @return int
     */
    protected function countBlocksByType(array $blocks, string $blockType): int
    {
        return count(array_filter(
            $blocks,
            fn($block) => $block['BlockType'] === $blockType
        ));
    }

    /**
     * Parse table data from TextExtract blocks
     * 
     * @param array $blocks
     * @return array Structured table data
     */
    public function parseTablesFromBlocks(array $blocks): array
    {
        $tables = [];
        $childRelationships = [];

        // Build map of child relationships
        foreach ($blocks as $block) {
            if (isset($block['Relationships'])) {
                foreach ($block['Relationships'] as $rel) {
                    if ($rel['Type'] === 'CHILD') {
                        foreach ($rel['Ids'] as $childId) {
                            $childRelationships[$childId] = $block['Id'];
                        }
                    }
                }
            }
        }

        // Extract table data
        foreach ($blocks as $block) {
            if ($block['BlockType'] === 'TABLE') {
                $tableData = $this->extractTableData($block, $blocks, $childRelationships);
                $tables[] = $tableData;
            }
        }

        return $tables;
    }

    /**
     * Extract structured data from a single table block
     * 
     * @param array $tableBlock
     * @param array $allBlocks
     * @param array $childRelationships
     * @return array
     */
    protected function extractTableData(array $tableBlock, array $allBlocks, array $childRelationships): array
    {
        $blockMap = [];
        foreach ($allBlocks as $block) {
            $blockMap[$block['Id']] = $block;
        }

        $cells = [];
        if (isset($tableBlock['Relationships'])) {
            foreach ($tableBlock['Relationships'] as $rel) {
                if ($rel['Type'] === 'CHILD') {
                    foreach ($rel['Ids'] as $cellId) {
                        if (isset($blockMap[$cellId])) {
                            $cells[] = $blockMap[$cellId];
                        }
                    }
                }
            }
        }

        return [
            'id' => $tableBlock['Id'],
            'confidence' => $tableBlock['Confidence'] ?? 0,
            'cell_count' => count($cells),
            'cells' => $cells,
        ];
    }

    /**
     * Extract forms (key-value pairs) from TextExtract blocks
     * 
     * @param array $blocks
     * @return array Form key-value pairs
     */
    public function parseFormsFromBlocks(array $blocks): array
    {
        $kvMap = [];
        $blockMap = [];

        foreach ($blocks as $block) {
            $blockMap[$block['Id']] = $block;
        }

        // Find KEY_VALUE_SET blocks
        foreach ($blocks as $block) {
            if ($block['BlockType'] === 'KEY_VALUE_SET' && $block['EntityTypes'][0] === 'KEY') {
                $keyText = $this->extractTextFromKeyValueSet($block, $blockMap);
                
                // Find corresponding VALUE
                if (isset($block['Relationships'])) {
                    foreach ($block['Relationships'] as $rel) {
                        if ($rel['Type'] === 'VALUE') {
                            $valueBlock = $blockMap[$rel['Ids'][0]] ?? null;
                            if ($valueBlock) {
                                $valueText = $this->extractTextFromKeyValueSet($valueBlock, $blockMap);
                                $kvMap[$keyText] = $valueText;
                            }
                        }
                    }
                }
            }
        }

        return $kvMap;
    }

    /**
     * Extract text from a KEY_VALUE_SET block
     * 
     * @param array $kvBlock
     * @param array $blockMap
     * @return string
     */
    protected function extractTextFromKeyValueSet(array $kvBlock, array $blockMap): string
    {
        $textParts = [];

        if (isset($kvBlock['Relationships'])) {
            foreach ($kvBlock['Relationships'] as $rel) {
                if ($rel['Type'] === 'CHILD') {
                    foreach ($rel['Ids'] as $childId) {
                        if (isset($blockMap[$childId]) && isset($blockMap[$childId]['Text'])) {
                            $textParts[] = $blockMap[$childId]['Text'];
                        }
                    }
                }
            }
        }

        return implode(' ', $textParts);
    }
}
