<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('civic_activities', function (Blueprint $table): void {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('title', 255);
            $table->text('description');
            $table->string('icon_emoji', 10)->default('🎯');
            $table->text('content')->nullable();
            $table->dateTime('event_date')->nullable();
            $table->time('event_start_time')->nullable();
            $table->time('event_end_time')->nullable();
            $table->string('location', 500);
            $table->text('location_details')->nullable();
            $table->string('target_audience', 255);
            $table->integer('max_participants')->nullable();
            $table->json('activity_details')->nullable(); // Flexible JSON for custom fields
            $table->string('status', 50)->default('upcoming'); // upcoming, ongoing, completed, cancelled
            $table->string('activity_type', 50)->default('community'); // community, workshop, forum, celebration
            $table->string('frequency', 50)->default('once'); // once, weekly, monthly, quarterly
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['status']);
            $table->index(['event_date']);
            $table->index(['sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('civic_activities');
    }
};
