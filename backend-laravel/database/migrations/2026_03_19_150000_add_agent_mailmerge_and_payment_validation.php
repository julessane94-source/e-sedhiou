<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table): void {
            $table->foreignId('payment_validated_by')
                ->nullable()
                ->after('paid_at')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('payment_validated_at')->nullable()->after('payment_validated_by');
        });

        Schema::create('mail_merge_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('request_type', 120);
            $table->string('file_path', 500);
            $table->string('original_name', 255);
            $table->timestamps();

            $table->unique(['user_id', 'request_type']);
            $table->index(['request_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_merge_templates');

        Schema::table('demandes', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('payment_validated_by');
            $table->dropColumn('payment_validated_at');
        });
    }
};