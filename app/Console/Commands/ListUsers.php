<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    protected $signature = 'users:list';
    protected $description = 'List all users with their login and role';

    public function handle()
    {
        $users = User::with('role')->get();

        $this->info('Liste des utilisateurs:');
        $this->info('');

        foreach ($users as $user) {
            $this->line("Login: {$user->login} | Rôle: {$user->role->libelle} | Mot de passe: password");
        }

        $this->info('');
        $this->info('Tous les mots de passe sont: password');

        return Command::SUCCESS;
    }
}
