<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 20)->default('citoyen')->after('email');
            // Rôles : admin, agent, citoyen
            $table->string('first_name', 120)->nullable()->after('name');
            $table->string('last_name', 120)->nullable()->after('first_name');
            $table->string('phone', 30)->nullable()->after('last_name');
            $table->string('address', 500)->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('address');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['role', 'first_name', 'last_name', 'phone', 'address', 'is_active', 'last_login_at']);
        });
    }
};
