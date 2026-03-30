<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('civic_courses', function (Blueprint $table): void {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('title', 255);
            $table->text('description');
            $table->string('icon_emoji', 10)->default('📚');
            $table->text('content')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->json('topics')->nullable(); // Array of topics
            $table->boolean('is_active')->default(true);
            $table->string('course_type', 50)->default('online'); // online, hybrid, etc.
            $table->string('difficulty_level', 50)->default('beginner'); // beginner, intermediate, advanced
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('civic_courses');
    }
};
