<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table): void {
            $table->string('processing_channel', 40)->default('counter')->after('processed_at');
            $table->string('processed_document_path', 500)->nullable()->after('processing_channel');
            $table->string('processed_document_name', 255)->nullable()->after('processed_document_path');

            $table->index(['processing_channel']);
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table): void {
            $table->dropIndex(['processing_channel']);
            $table->dropColumn([
                'processing_channel',
                'processed_document_path',
                'processed_document_name',
            ]);
        });
    }
};
