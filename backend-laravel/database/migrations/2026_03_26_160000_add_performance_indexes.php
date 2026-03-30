<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add missing indexes for performance
     */
    public function up(): void
    {
        // Add indexes to demandes table
        try {
            Schema::table('demandes', function (Blueprint $table) {
                $table->index(['user_id']);
                $table->index(['status']);
                $table->index(['created_at']);
            });
        } catch (\Exception $e) {
            // Index might already exist, skip
        }

        // Add indexes to messages table
        try {
            Schema::table('messages', function (Blueprint $table) {
                $table->index(['sender_id']);
                $table->index(['receiver_id']);
                $table->index(['created_at']);
            });
        } catch (\Exception $e) {
            // Index might already exist, skip
        }

        // Add indexes to users table
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['email']);
                $table->index(['role']);
            });
        } catch (\Exception $e) {
            // Index might already exist, skip
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('demandes', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
                $table->dropIndex(['status']);
                $table->dropIndex(['created_at']);
            });
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropIndex(['sender_id']);
                $table->dropIndex(['receiver_id']);
                $table->dropIndex(['created_at']);
            });
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['email']);
                $table->dropIndex(['role']);
            });
        } catch (\Exception $e) {
            // Ignore
        }
    }
};

