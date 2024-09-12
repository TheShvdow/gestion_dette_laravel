<?php 

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\AuthentificationServiceInterface;


class AuthentificationServicePassport implements AuthentificationServiceInterface {
    
    public function authentificate(array $credentials) {
        $user = User::where('login', $credentials['login'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->accessToken;
        //need to add refresh token here
        



        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ];
    }

    public function logout($user) {
        // Supprime tous les tokens associés à l'utilisateur
        $user->tokens()->delete();

        // Retourne un message de succès
        return ['message' => 'Successfully logged out'];
    }
}