<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table): void {
            $table->id();
            $table->string('reference', 32)->unique();
            $table->string('request_type', 120);
            $table->string('email', 190);
            $table->string('first_name', 120);
            $table->string('last_name', 120);
            $table->date('birth_date');
            $table->string('birth_place', 190);
            $table->string('register_number', 120);
            $table->text('address');
            $table->string('parent_one_first_name', 120);
            $table->string('parent_one_last_name', 120);
            $table->string('parent_two_first_name', 120);
            $table->string('parent_two_last_name', 120);
            $table->text('details')->nullable();
            $table->string('attachment_url', 2000)->nullable();
            $table->string('attachment_name', 255)->nullable();
            $table->string('status', 40)->default('pending');
            $table->string('source', 80)->default('wordpress');
            $table->unsignedBigInteger('wp_request_id')->nullable();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['email']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
