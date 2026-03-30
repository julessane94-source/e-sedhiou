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
        Schema::create('activity_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('civic_activity_id')->constrained('civic_activities')->cascadeOnDelete();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('attended_at')->nullable();
            $table->string('status')->default('registered');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'civic_activity_id']);
            $table->index(['civic_activity_id']);
            $table->index(['user_id']);
            $table->index(['status']);
            $table->index(['registered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_registrations');
    }
};
