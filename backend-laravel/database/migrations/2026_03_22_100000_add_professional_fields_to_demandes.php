<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table): void {
            // 1. Informations Professionnelles
            $table->string('profession_sector', 100)->nullable()->after('address')->comment('Agriculture, Élevage, Informatique/Digital, BTP/Construction, Commerce, Transport, Artisanat, Santé, Éducation, Autre');
            $table->string('profession_title', 255)->nullable()->after('profession_sector')->comment('Métier précis: Maraîcher, Développeur Laravel, etc.');
            $table->string('education_level', 100)->nullable()->after('profession_title')->comment('Aucun, CFEE, BFEM, BAC, BTS/Licence, Master, Doctorat, Diplôme technique');
            $table->integer('years_experience')->nullable()->after('education_level')->comment('Années d\'expérience');

            // 2. Compétences & Disponibilité
            $table->text('skills')->nullable()->after('years_experience')->comment('Tags séparés par virgules: Soudure à l\'arc, Gestion d\'équipe, etc.');
            $table->string('current_status', 50)->nullable()->after('skills')->comment('Étudiant, En recherche d\'emploi, Indépendant/Auto-entrepreneur, Salarié');
            $table->boolean('available_for_municipality')->default(false)->after('current_status')->comment('Disponible pour projets/missions locales');

            // 3. Justificatifs (Optionnel)
            $table->string('cv_file_path', 500)->nullable()->after('available_for_municipality')->comment('Chemin du CV (PDF)');
            $table->string('cv_file_name', 255)->nullable()->after('cv_file_path')->comment('Nom original du CV');
            $table->string('portfolio_url', 500)->nullable()->after('cv_file_name')->comment('Lien Portfolio/LinkedIn/Site personnel');

            // Index pour recherches
            $table->index(['profession_sector']);
            $table->index(['current_status']);
            $table->index(['available_for_municipality']);
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table): void {
            $table->dropIndex(['profession_sector']);
            $table->dropIndex(['current_status']);
            $table->dropIndex(['available_for_municipality']);
            $table->dropColumn([
                'profession_sector',
                'profession_title',
                'education_level',
                'years_experience',
                'skills',
                'current_status',
                'available_for_municipality',
                'cv_file_path',
                'cv_file_name',
                'portfolio_url',
            ]);
        });
    }
};
