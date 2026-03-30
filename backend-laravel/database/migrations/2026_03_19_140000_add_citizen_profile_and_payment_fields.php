<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->date('birth_date')->nullable()->after('address');
            $table->string('register_number', 120)->nullable()->after('birth_date');
            $table->string('citizen_number', 40)->nullable()->unique()->after('register_number');
        });

        Schema::table('demandes', function (Blueprint $table): void {
            $table->string('payment_status', 20)->default('unpaid')->after('status');
            $table->string('payment_reference', 60)->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_reference');
            $table->index(['payment_status']);
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table): void {
            $table->dropIndex(['payment_status']);
            $table->dropColumn(['payment_status', 'payment_reference', 'paid_at']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['citizen_number']);
            $table->dropColumn(['birth_date', 'register_number', 'citizen_number']);
        });
    }
};