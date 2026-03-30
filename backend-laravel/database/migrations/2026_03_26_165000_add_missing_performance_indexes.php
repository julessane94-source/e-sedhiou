<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add missing indexes for performance optimization
     */
    public function up(): void
    {
        // Add created_at index to civic_courses (fixes slow active() scope)
        try {
            DB::statement('ALTER TABLE `civic_courses` ADD INDEX `idx_created_at` (`created_at`)');
        } catch (\Exception $e) {
            // Index already exists
        }

        // Add status index to activity_registrations
        try {
            DB::statement('ALTER TABLE `activity_registrations` ADD INDEX `idx_status` (`status`)');
        } catch (\Exception $e) {
            // Index already exists
        }
        
        // Add registered_at index to activity_registrations
        try {
            DB::statement('ALTER TABLE `activity_registrations` ADD INDEX `idx_registered_at` (`registered_at`)');
        } catch (\Exception $e) {
            // Index already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE `civic_courses` DROP INDEX `idx_created_at`');
        } catch (\Exception $e) {
            // Index doesn't exist
        }

        try {
            DB::statement('ALTER TABLE `activity_registrations` DROP INDEX `idx_status`');
        } catch (\Exception $e) {
            // Index doesn't exist
        }

        try {
            DB::statement('ALTER TABLE `activity_registrations` DROP INDEX `idx_registered_at`');
        } catch (\Exception $e) {
            // Index doesn't exist
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select(DB::raw("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$index}'"));
        return !empty($indexes);
    }
};
