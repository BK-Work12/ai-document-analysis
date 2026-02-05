<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->text('correction_feedback')->nullable()->after('notes');
            $table->timestamp('correction_requested_at')->nullable()->after('correction_feedback');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['correction_feedback', 'correction_requested_at']);
        });
    }
};
