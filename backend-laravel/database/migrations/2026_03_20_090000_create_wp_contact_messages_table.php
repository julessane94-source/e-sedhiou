<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wp_contact_messages', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('wp_contact_message_id')->nullable()->unique();
            $table->string('sender_name', 190);
            $table->string('sender_email', 190);
            $table->string('subject', 190)->nullable();
            $table->text('message');
            $table->string('source', 80)->default('wordpress');
            $table->string('source_url', 2048)->nullable();
            $table->string('sender_ip', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->string('reply_subject', 190)->nullable();
            $table->text('reply_body')->nullable();
            $table->string('reply_status', 32)->nullable();
            $table->text('reply_error')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['sender_email', 'created_at']);
            $table->index(['reply_status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wp_contact_messages');
    }
};
