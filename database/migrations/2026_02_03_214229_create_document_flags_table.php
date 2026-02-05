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
        Schema::create('document_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flagged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('flag_type');
            $table->text('description')->nullable();
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['document_id', 'resolved_at']);
            $table->index('severity');
            $table->index('flag_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_flags');
    }
};
