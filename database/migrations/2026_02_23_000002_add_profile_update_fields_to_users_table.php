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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('profile_update_required')->default(false)->after('receives_notifications');
            $table->text('profile_update_note')->nullable()->after('profile_update_required');
            $table->timestamp('profile_update_requested_at')->nullable()->after('profile_update_note');
            $table->foreignId('profile_update_requested_by')
                ->nullable()
                ->after('profile_update_requested_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('profile_update_requested_by');
            $table->dropColumn([
                'profile_update_required',
                'profile_update_note',
                'profile_update_requested_at',
            ]);
        });
    }
};
