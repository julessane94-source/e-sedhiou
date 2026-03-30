<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CivicCourse;
use App\Models\CivicActivity;
use Carbon\Carbon;

class CivicContentSeeder extends Seeder
{
    public function run(): void
    {
        // Courses
        CivicCourse::create([
            'title' => 'La Citoyenneté',
            'slug' => 'la-citoyennete',
            'description' => 'Comprendre les principes fondamentaux de la citoyenneté et nos responsabilités envers la communauté.',
            'icon_emoji' => '🏛️',
            'content' => 'Ce cours explore les concepts essentiels de la citoyenneté, incluant les droits, les devoirs et la participation active dans la vie civique.',
            'duration_minutes' => 45,
            'topics' => ['Définition de la citoyenneté', 'Droits civiques', 'Devoirs du citoyen', 'Participation démocratique'],
            'course_type' => 'hybrid',
            'difficulty_level' => 'beginner',
            'is_active' => true,
            'sort_order' => 1,
            'created_by' => 1,
        ]);

        CivicCourse::create([
            'title' => 'Le Patriotisme',
            'slug' => 'le-patriotisme',
            'description' => 'Découvrez comment le patriotisme inspire l\'engagement social et le dévouement à sa nation.',
            'icon_emoji' => '🇸🇳',
            'content' => 'Un cours sur les valeurs patriotiques, l\'histoire nationale et comment cultiver un amour sain pour sa patrie.',
            'duration_minutes' => 50,
            'topics' => ['L\'amour de la patrie', 'Valeurs nationales', 'Histoire du Sénégal', 'Engagement citoyen'],
            'course_type' => 'online',
            'difficulty_level' => 'intermediate',
            'is_active' => true,
            'sort_order' => 2,
            'created_by' => 1,
        ]);

        CivicCourse::create([
            'title' => 'L\'Olympisme',
            'slug' => 'l-olympisme',
            'description' => 'Explorez les valeurs olympiques d\'excellence, d\'amitié et de respect à travers le sport et la vie civique.',
            'icon_emoji' => '🏅',
            'content' => 'Ce cours examine les principes olympiques et comment ils peuvent nous guider vers une société plus juste et unie.',
            'duration_minutes' => 60,
            'topics' => ['Excellence olympique', 'Amitié et respect', 'Sport et citoyenneté', 'Valeurs olympiques dans la vie quotidienne'],
            'course_type' => 'hybrid',
            'difficulty_level' => 'intermediate',
            'is_active' => true,
            'sort_order' => 3,
            'created_by' => 1,
        ]);

        // Activities
        CivicActivity::create([
            'title' => 'Engagement Citoyenne',
            'slug' => 'engagement-citoyenne',
            'description' => 'Participez à des actions concrètes pour améliorer notre communauté et renforcer nos liens sociaux.',
            'icon_emoji' => '🤝',
            'content' => 'Une série d\'initiatives citoyennes visant à mobiliser les jeunes et les adultes autour de projets collectifs.',
            'event_date' => Carbon::now()->addDays(7),
            'event_start_time' => '09:00',
            'event_end_time' => '12:00',
            'location' => 'Mairie de Thièès',
            'location_details' => 'Salle des réunions, RDC',
            'target_audience' => 'Tous les citoyens, 15 ans et plus',
            'max_participants' => 50,
            'status' => 'upcoming',
            'activity_type' => 'community',
            'frequency' => 'weekly',
            'is_active' => true,
            'sort_order' => 1,
            'created_by' => 1,
        ]);

        CivicActivity::create([
            'title' => 'Formation pour la Jeunesse',
            'slug' => 'formation-pour-la-jeunesse',
            'description' => 'Programme de formation destiné aux jeunes pour développer leurs compétences civiques et leadership.',
            'icon_emoji' => '📚',
            'content' => 'Des ateliers interactifs et des séminaires animés par des experts pour former les jeunes générations aux enjeux civiques actuels.',
            'event_date' => Carbon::now()->addDays(14),
            'event_start_time' => '14:00',
            'event_end_time' => '17:00',
            'location' => 'Centre Culturel de Thièès',
            'location_details' => 'Auditorium principal',
            'target_audience' => 'Jeunes de 13 à 25 ans',
            'max_participants' => 100,
            'status' => 'upcoming',
            'activity_type' => 'workshop',
            'frequency' => 'monthly',
            'is_active' => true,
            'sort_order' => 2,
            'created_by' => 1,
        ]);

        CivicActivity::create([
            'title' => 'Réunion du CRJ',
            'slug' => 'reunion-crj',
            'description' => 'Session mensuelle du Conseil de la Représentation de la Jeunesse pour discuter des enjeux locaux.',
            'icon_emoji' => '💬',
            'content' => 'Réunion ordinaire du CRJ où tous les représentants jeunes se rassemblent pour débattre et proposer des solutions aux problèmes de la communauté.',
            'event_date' => Carbon::now()->addDays(21),
            'event_start_time' => '18:00',
            'event_end_time' => '20:00',
            'location' => 'Salle de Réunion Municipale',
            'location_details' => 'Étage 2, Bureau du Maire',
            'target_audience' => 'Représentants CRJ et jeunes intéressés',
            'max_participants' => 40,
            'status' => 'upcoming',
            'activity_type' => 'forum',
            'frequency' => 'monthly',
            'is_active' => true,
            'sort_order' => 3,
            'created_by' => 1,
        ]);
    }
}
