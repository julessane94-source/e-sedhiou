<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table): void {
            $table->dropForeign(['demande_id']);
            $table->unsignedBigInteger('demande_id')->nullable()->change();
            $table->foreign('demande_id')->references('id')->on('demandes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table): void {
            $table->dropForeign(['demande_id']);
            $table->unsignedBigInteger('demande_id')->nullable(false)->change();
            $table->foreign('demande_id')->references('id')->on('demandes')->cascadeOnDelete();
        });
    }
};
