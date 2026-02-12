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
        Schema::create('email_suppressions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->enum('reason', ['bounce', 'complaint', 'unsubscribe'])->default('bounce');
            $table->string('bounce_type')->nullable();
            $table->string('complaint_type')->nullable();
            $table->timestamp('suppressed_at')->useCurrent();
            $table->timestamps();

            $table->index('email');
            $table->index('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_suppressions');
    }
};
