<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add attachments to civic_courses
        Schema::table('civic_courses', function (Blueprint $table): void {
            $table->string('document_path', 500)->nullable()->after('content');
            $table->string('image_path', 500)->nullable()->after('document_path');
            $table->string('document_name', 255)->nullable()->after('image_path');
            $table->string('image_name', 255)->nullable()->after('document_name');
        });

        // Add attachments to civic_activities
        Schema::table('civic_activities', function (Blueprint $table): void {
            $table->string('document_path', 500)->nullable()->after('content');
            $table->string('image_path', 500)->nullable()->after('document_path');
            $table->string('document_name', 255)->nullable()->after('image_path');
            $table->string('image_name', 255)->nullable()->after('document_name');
        });
    }

    public function down(): void
    {
        Schema::table('civic_courses', function (Blueprint $table): void {
            $table->dropColumn(['document_path', 'image_path', 'document_name', 'image_name']);
        });

        Schema::table('civic_activities', function (Blueprint $table): void {
            $table->dropColumn(['document_path', 'image_path', 'document_name', 'image_name']);
        });
    }
};
