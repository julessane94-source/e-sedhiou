<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Compte admin par défaut — CHANGER LE MOT DE PASSE EN PRODUCTION
        User::firstOrCreate(
            ['email' => 'admin@mairie.local'],
            [
                'name'       => 'Administrateur Mairie',
                'first_name' => 'Admin',
                'last_name'  => 'Mairie',
                'email'      => 'admin@mairie.local',
                'password'   => Hash::make('Admin@2026!'),
                'role'       => User::ROLE_ADMIN,
                'is_active'  => true,
            ]
        );

        // Agent de démonstration
        User::firstOrCreate(
            ['email' => 'agent@mairie.local'],
            [
                'name'       => 'Agent Démo',
                'first_name' => 'Jean',
                'last_name'  => 'Dupont',
                'email'      => 'agent@mairie.local',
                'password'   => Hash::make('Agent@2026!'),
                'role'       => User::ROLE_AGENT,
                'is_active'  => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'superviseur@mairie.local'],
            [
                'name'       => 'Superviseur Demo',
                'first_name' => 'Marc',
                'last_name'  => 'Kouadio',
                'email'      => 'superviseur@mairie.local',
                'password'   => Hash::make('Superviseur@2026!'),
                'role'       => User::ROLE_SUPERVISEUR,
                'is_active'  => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'sup@gmail.com'],
            [
                'name'       => 'Superviseur Baye',
                'first_name' => 'Baye',
                'last_name'  => 'Superviseur',
                'password'   => Hash::make('Baye1994@'),
                'role'       => User::ROLE_SUPERVISEUR,
                'is_active'  => true,
            ]
        );

        $this->command->info('Comptes admin, superviseur et agent crees :');
        $this->command->info('  admin@mairie.local  / Admin@2026!');
        $this->command->info('  superviseur@mairie.local  / Superviseur@2026!');
        $this->command->info('  sup@gmail.com  / Baye1994@');
        $this->command->info('  agent@mairie.local  / Agent@2026!');
        $this->command->warn('  ⚠ Changez ces mots de passe en production !');
    }
}
