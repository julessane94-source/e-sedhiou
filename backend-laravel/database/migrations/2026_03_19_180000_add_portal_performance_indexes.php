<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table): void {
            $table->index(['user_id', 'created_at'], 'demandes_user_created_at_idx');
            $table->index(['agent_id', 'created_at'], 'demandes_agent_created_at_idx');
            $table->index(['agent_id', 'status'], 'demandes_agent_status_idx');
            $table->index(['status', 'agent_id', 'created_at'], 'demandes_status_agent_created_at_idx');
            $table->index(['agent_id', 'request_type', 'payment_status', 'created_at'], 'demandes_agent_type_payment_created_at_idx');
        });

        Schema::table('messages', function (Blueprint $table): void {
            $table->index(['receiver_id', 'created_at'], 'messages_receiver_created_at_idx');
            $table->index(['sender_id', 'created_at'], 'messages_sender_created_at_idx');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->index(['role', 'is_active'], 'users_role_active_idx');
        });

        Schema::table('activity_logs', function (Blueprint $table): void {
            $table->index(['created_at'], 'activity_logs_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table): void {
            $table->dropIndex('activity_logs_created_at_idx');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('users_role_active_idx');
        });

        Schema::table('messages', function (Blueprint $table): void {
            $table->dropIndex('messages_receiver_created_at_idx');
            $table->dropIndex('messages_sender_created_at_idx');
        });

        Schema::table('demandes', function (Blueprint $table): void {
            $table->dropIndex('demandes_user_created_at_idx');
            $table->dropIndex('demandes_agent_created_at_idx');
            $table->dropIndex('demandes_agent_status_idx');
            $table->dropIndex('demandes_status_agent_created_at_idx');
            $table->dropIndex('demandes_agent_type_payment_created_at_idx');
        });
    }
};