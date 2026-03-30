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
        Schema::create('course_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('civic_course_id')->constrained('civic_courses')->cascadeOnDelete();
            $table->integer('view_count')->default(1);
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'civic_course_id']);
            $table->index(['civic_course_id']);
            $table->index(['user_id']);
            $table->index(['viewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_views');
    }
};
