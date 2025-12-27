<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            //RoleSeeder::class,
            //UsersTableSeeder::class,
            //ClientsTableSeeder::class,
            //ClientsUsersTableSeeder::class,
            //ArticlesTableSeeder::class,
            DettesTableSeeder::class,
        ]);

        // $roleBoutiquier = Role::where('libelle', 'Boutiquier')->first();

        // if (!$roleBoutiquier) {

        //     $this->command->error('Le rôle Boutiquier est introuvable');
        //     return;
        // }

        //     // Création du User
        //     $user = User::create([
        //         'nom' => 'User',
        //         'prenom' => 'Test',
        //         'login' => 'test.user',
        //         'password' => Hash::make('password123'),
        //         'roleId' => $roleBoutiquier->id,
        //         'active' => "oui",
        //     ]);

        //     // Création du Client lié au User
        //     Client::create([
        //         'surname' => 'Test User',
        //         'telephone' => '770000000',
        //         'adresse' => 'Dakar',
        //         'user_id' => $user->id,
        //     ]);




        }
    }

