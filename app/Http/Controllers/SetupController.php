<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Traits\RestResponseTrait;

class SetupController extends Controller
{
    use RestResponseTrait;

    /**
     * Setup endpoint to create the boutiquier user
     * This should be disabled in production after initial setup
     */
    public function createBoutiquier(): JsonResponse
    {
        try {
            // Check if Role exists
            $role = Role::where('libelle', 'Boutiquier')->first();

            if (!$role) {
                return $this->sendResponse(null, 'Le rôle Boutiquier n\'existe pas. Exécutez les migrations et seeders d\'abord.', 500);
            }

            // Check if user already exists
            $existingUser = User::where('login', 'boutiquer')->first();

            if ($existingUser) {
                // Update password
                $existingUser->password = Hash::make('Boutiquier@2024');
                $existingUser->save();

                return $this->sendResponse([
                    'login' => 'boutiquer',
                    'password' => 'Boutiquier@2024',
                    'message' => 'Utilisateur mis à jour'
                ], 'Mot de passe de l\'utilisateur boutiquer mis à jour avec succès', 200);
            }

            // Create new user
            $user = User::create([
                'nom' => 'Boutiquier',
                'prenom' => 'Principal',
                'login' => 'boutiquer',
                'password' => Hash::make('Boutiquier@2024'),
                'roleId' => $role->id,
            ]);

            return $this->sendResponse([
                'login' => 'boutiquer',
                'password' => 'Boutiquier@2024',
                'user_id' => $user->id
            ], 'Utilisateur boutiquer créé avec succès', 201);

        } catch (\Exception $e) {
            return $this->sendResponse(null, 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage(), 500);
        }
    }

    /**
     * List all users (for debugging)
     */
    public function listUsers(): JsonResponse
    {
        try {
            $users = User::with('role')->get(['id', 'login', 'nom', 'prenom', 'roleId']);

            return $this->sendResponse($users, 'Liste des utilisateurs', 200);

        } catch (\Exception $e) {
            return $this->sendResponse(null, 'Erreur: ' . $e->getMessage(), 500);
        }
    }
}
