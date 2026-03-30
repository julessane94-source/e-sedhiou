<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'birth_place')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('birth_place', 190)->nullable()->after('birth_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'birth_place')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('birth_place');
            });
        }
    }
};
