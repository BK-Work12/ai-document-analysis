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
        Schema::create('admin_user_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title')->default('All Documents Conversation');
            $table->enum('status', ['active', 'archived', 'closed'])->default('active');
            $table->longText('context')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->unique(['client_user_id', 'admin_user_id']);
            $table->index('status');
            $table->index('last_message_at');
        });

        Schema::create('admin_user_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_conversation_id')
                ->constrained('admin_user_conversations')
                ->cascadeOnDelete();
            $table->enum('sender_type', ['user', 'ai']);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('message');
            $table->enum('role', ['user', 'assistant']);
            $table->json('metadata')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['admin_user_conversation_id', 'created_at'], 'auc_messages_conversation_created_idx');
            $table->index('sender_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user_chat_messages');
        Schema::dropIfExists('admin_user_conversations');
    }
};
