<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citizen_registry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Informations civiles
            $table->string('first_name', 120);
            $table->string('last_name', 120);
            $table->string('email', 190)->unique();
            $table->string('phone', 30);
            $table->date('birth_date');
            $table->string('birth_place', 190);
            $table->string('register_number', 120);
            $table->text('address');
            
            // Informations professionnelles
            $table->string('profession_sector', 100);
            $table->string('profession_title', 255);
            $table->string('education_level', 100);
            $table->integer('years_experience')->default(0);
            
            // Compétences & Disponibilité
            $table->text('skills');
            $table->string('current_status', 50);
            $table->boolean('available_for_municipality')->default(false);
            
            // Justificatifs
            $table->string('cv_file_path', 500)->nullable();
            $table->string('cv_file_name', 255)->nullable();
            $table->string('portfolio_url', 500)->nullable();
            
            // Métadonnées
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizen_registry');
    }
};
