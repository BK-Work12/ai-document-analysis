<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add all required columns for document processing
        Schema::table('documents', function (Blueprint $table) {
            // TextExtract fields
            if (!Schema::hasColumn('documents', 'extraction_status')) {
                $table->enum('extraction_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            }
            if (!Schema::hasColumn('documents', 'extracted_text')) {
                $table->longText('extracted_text')->nullable();
            }
            if (!Schema::hasColumn('documents', 'extraction_started_at')) {
                $table->timestamp('extraction_started_at')->nullable();
            }
            if (!Schema::hasColumn('documents', 'extraction_completed_at')) {
                $table->timestamp('extraction_completed_at')->nullable();
            }
            if (!Schema::hasColumn('documents', 'extraction_error')) {
                $table->text('extraction_error')->nullable();
            }
            if (!Schema::hasColumn('documents', 'text_extraction_metadata')) {
                $table->json('text_extraction_metadata')->nullable();
            }
            
            // Bedrock Analysis fields
            if (!Schema::hasColumn('documents', 'analysis_status')) {
                $table->enum('analysis_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            }
            if (!Schema::hasColumn('documents', 'analysis_result')) {
                $table->json('analysis_result')->nullable();
            }
            if (!Schema::hasColumn('documents', 'analysis_metadata')) {
                $table->json('analysis_metadata')->nullable();
            }
            if (!Schema::hasColumn('documents', 'classified_doc_type')) {
                $table->string('classified_doc_type')->nullable();
            }
            if (!Schema::hasColumn('documents', 'confidence_score')) {
                $table->decimal('confidence_score', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('documents', 'risk_flags')) {
                $table->json('risk_flags')->nullable();
            }
            if (!Schema::hasColumn('documents', 'missing_fields')) {
                $table->json('missing_fields')->nullable();
            }
            if (!Schema::hasColumn('documents', 'analysis_started_at')) {
                $table->timestamp('analysis_started_at')->nullable();
            }
            if (!Schema::hasColumn('documents', 'analysis_completed_at')) {
                $table->timestamp('analysis_completed_at')->nullable();
            }
            if (!Schema::hasColumn('documents', 'analysis_error')) {
                $table->text('analysis_error')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $toDrop = [];
            
            if (Schema::hasColumn('documents', 'extracted_text')) {
                $toDrop[] = 'extracted_text';
            }
            if (Schema::hasColumn('documents', 'extraction_status')) {
                $toDrop[] = 'extraction_status';
            }
            if (Schema::hasColumn('documents', 'extraction_started_at')) {
                $toDrop[] = 'extraction_started_at';
            }
            if (Schema::hasColumn('documents', 'extraction_completed_at')) {
                $toDrop[] = 'extraction_completed_at';
            }
            if (Schema::hasColumn('documents', 'extraction_error')) {
                $toDrop[] = 'extraction_error';
            }
            if (Schema::hasColumn('documents', 'text_extraction_metadata')) {
                $toDrop[] = 'text_extraction_metadata';
            }
            if (Schema::hasColumn('documents', 'analysis_status')) {
                $toDrop[] = 'analysis_status';
            }
            if (Schema::hasColumn('documents', 'analysis_result')) {
                $toDrop[] = 'analysis_result';
            }
            if (Schema::hasColumn('documents', 'analysis_metadata')) {
                $toDrop[] = 'analysis_metadata';
            }
            if (Schema::hasColumn('documents', 'classified_doc_type')) {
                $toDrop[] = 'classified_doc_type';
            }
            if (Schema::hasColumn('documents', 'confidence_score')) {
                $toDrop[] = 'confidence_score';
            }
            if (Schema::hasColumn('documents', 'risk_flags')) {
                $toDrop[] = 'risk_flags';
            }
            if (Schema::hasColumn('documents', 'missing_fields')) {
                $toDrop[] = 'missing_fields';
            }
            if (Schema::hasColumn('documents', 'analysis_started_at')) {
                $toDrop[] = 'analysis_started_at';
            }
            if (Schema::hasColumn('documents', 'analysis_completed_at')) {
                $toDrop[] = 'analysis_completed_at';
            }
            if (Schema::hasColumn('documents', 'analysis_error')) {
                $toDrop[] = 'analysis_error';
            }
            
            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
