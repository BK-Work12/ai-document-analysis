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
        // Tags table for classification categories
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('gray')->comment('Hex color for UI');
            $table->string('category')->nullable()->comment('Tag category: financial, legal, personal, risk, etc');
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index('category');
            $table->index('active');
        });

        // Document to Tag pivot table
        Schema::create('document_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->text('reason')->nullable()->comment('Why this tag was applied');
            $table->string('confidence')->nullable()->comment('AI confidence: high|medium|low');
            $table->boolean('manual')->default(false)->comment('Was this tag manually added');
            $table->foreignId('tagged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tagged_at');
            $table->timestamps();

            $table->unique(['document_id', 'tag_id']);
            $table->index('manual');
            $table->index('confidence');
        });

        // Document-specific conversations for admin chat
        Schema::create('document_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('title')->nullable()->comment('Conversation title');
            $table->string('status')->default('active')->comment('active|archived|closed');
            $table->longText('context')->nullable()->comment('Document context sent to Bedrock');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index('document_id');
            $table->index('user_id');
            $table->index('status');
        });

        // Chat messages for document conversations
        Schema::create('document_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_conversation_id')->constrained('document_conversations')->cascadeOnDelete();
            $table->enum('sender_type', ['user', 'ai'])->comment('who sent the message');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('message');
            $table->string('role')->default('assistant')->comment('assistant|user for Bedrock');
            $table->json('metadata')->nullable()->comment('model, tokens, citations, etc');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index('document_conversation_id');
            $table->index('sender_type');
            $table->index('user_id');
        });

        // Auto-review results
        Schema::create('document_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->enum('review_status', ['pending', 'approved', 'needs_revision', 'rejected'])->default('pending');
            $table->text('review_notes')->nullable();
            $table->json('auto_review_results')->nullable()->comment('Auto-review findings');
            $table->integer('quality_score')->nullable()->comment('0-100 overall quality');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('auto_reviewed_at')->nullable();
            $table->timestamps();

            $table->index('document_id');
            $table->index('review_status');
            $table->index('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_reviews');
        Schema::dropIfExists('document_chat_messages');
        Schema::dropIfExists('document_conversations');
        Schema::dropIfExists('document_tags');
        Schema::dropIfExists('tags');
    }
};
