<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateBoutiquierUser extends Command
{
    protected $signature = 'user:create-boutiquier';
    protected $description = 'Create the boutiquier user with specific credentials';

    public function handle()
    {
        $role = Role::where('libelle', 'Boutiquier')->first();

        if (!$role) {
            $this->error('Le rôle Boutiquier n\'existe pas. Assurez-vous d\'avoir exécuté les seeders.');
            return Command::FAILURE;
        }

        // Vérifier si l'utilisateur existe déjà
        $existingUser = User::where('login', 'boutiquer')->first();

        if ($existingUser) {
            $this->info('L\'utilisateur "boutiquer" existe déjà. Mise à jour du mot de passe...');
            $existingUser->password = Hash::make('Boutiquier@2024');
            $existingUser->save();
            $this->info('Mot de passe mis à jour avec succès !');
        } else {
            $user = User::create([
                'nom' => 'Boutiquier',
                'prenom' => 'Principal',
                'login' => 'boutiquer',
                'password' => Hash::make('Boutiquier@2024'),
                'roleId' => $role->id,
            ]);

            $this->info('Utilisateur "boutiquer" créé avec succès !');
        }

        $this->info('');
        $this->info('Identifiants:');
        $this->info('Login: boutiquer');
        $this->info('Mot de passe: Boutiquier@2024');

        return Command::SUCCESS;
    }
}
