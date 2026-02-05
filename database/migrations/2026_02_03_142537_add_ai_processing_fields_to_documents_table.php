<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // TextExtract fields
            $table->enum('extraction_status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->after('status');
            $table->longText('extracted_text')->nullable()->after('extraction_status');
            $table->timestamp('extraction_started_at')->nullable()->after('extracted_text');
            $table->timestamp('extraction_completed_at')->nullable()->after('extraction_started_at');
            $table->text('extraction_error')->nullable()->after('extraction_completed_at');
            
            // Bedrock Analysis fields
            $table->enum('analysis_status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->after('extraction_error');
            $table->json('analysis_results')->nullable()->after('analysis_status');
            $table->string('classified_doc_type')->nullable()->after('analysis_results');
            $table->decimal('confidence_score', 5, 2)->nullable()->after('classified_doc_type');
            $table->json('identified_risks')->nullable()->after('confidence_score');
            $table->timestamp('analysis_started_at')->nullable()->after('identified_risks');
            $table->timestamp('analysis_completed_at')->nullable()->after('analysis_started_at');
            $table->text('analysis_error')->nullable()->after('analysis_completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'extraction_status',
                'extracted_text',
                'extraction_started_at',
                'extraction_completed_at',
                'extraction_error',
                'analysis_status',
                'analysis_results',
                'classified_doc_type',
                'confidence_score',
                'identified_risks',
                'analysis_started_at',
                'analysis_completed_at',
                'analysis_error',
            ]);
        });
    }
};
