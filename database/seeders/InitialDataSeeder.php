<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎯 Création des rôles...');

        // Créer les rôles
        $roles = [
            ['libelle' => 'Admin'],
            ['libelle' => 'Boutiquier'],
            ['libelle' => 'Client'],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate($roleData);
            $this->command->info("✓ Rôle créé : {$role->libelle}");
        }

        $this->command->newLine();
        $this->command->info('👤 Création de l\'utilisateur Admin...');

        // Récupérer le rôle Admin
        $adminRole = Role::where('libelle', 'Admin')->first();

        // Créer l'admin
        $admin = User::firstOrCreate(
            ['login' => 'admin'],
            [
                'nom' => 'Super',
                'prenom' => 'Admin',
                'password' => Hash::make('Admin@2024'),
                'roleId' => $adminRole->id,
                'active' => 'oui',
            ]
        );

        $this->command->info("✓ Admin créé : {$admin->login}");

        $this->command->newLine();
        $this->command->info('👔 Création de l\'utilisateur Boutiquier...');

        // Récupérer le rôle Boutiquier
        $boutiquierRole = Role::where('libelle', 'Boutiquier')->first();

        // Créer le boutiquier
        $boutiquier = User::firstOrCreate(
            ['login' => 'boutiquier'],
            [
                'nom' => 'Diallo',
                'prenom' => 'Amadou',
                'password' => Hash::make('Boutiquier@2024'),
                'roleId' => $boutiquierRole->id,
                'active' => 'oui',
            ]
        );

        $this->command->info("✓ Boutiquier créé : {$boutiquier->login}");

        $this->command->newLine();
        $this->command->info('📊 Résumé :');
        $this->command->info('  - Rôles : ' . Role::count());
        $this->command->info('  - Utilisateurs : ' . User::count());

        $this->command->newLine();
        $this->command->info('✅ Initialisation terminée !');
        $this->command->newLine();

        $this->command->warn('🔑 Informations de connexion :');
        $this->command->line('  Admin:');
        $this->command->line('    Login    : admin');
        $this->command->line('    Password : Admin@2024');
        $this->command->newLine();
        $this->command->line('  Boutiquier:');
        $this->command->line('    Login    : boutiquier');
        $this->command->line('    Password : Boutiquier@2024');
        $this->command->newLine();
    }
}
