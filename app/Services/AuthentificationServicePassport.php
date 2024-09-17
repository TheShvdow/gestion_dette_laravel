<?php 

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\AuthentificationServiceInterface;



class AuthentificationServicePassport implements AuthentificationServiceInterface {
    
    public function authentificate(array $credentials) {
        $user = User::where('login', $credentials['login'])->firstOrFail();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->accessToken;
        //need to add refresh token here    
        $newRefreshToken = Str::random(60);
        $user->refresh_token = hash('sha256', $newRefreshToken);
        $user->save();

       
        



        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'refresh_token' => $newRefreshToken,
            'user' => $user
        ];
    }
    

    public function logout($user) {
        // Supprime tous les tokens associés à l'utilisateur
        $user->tokens()->delete();

        // Deconnecte l'utilisateur de la session actuelle
        Auth::logout();

        // Invalidate la session actuelle
        

        // Retourne un message de succès
        return ['message' => 'Successfully logged out'];
    }
}