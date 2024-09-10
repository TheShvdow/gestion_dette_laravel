<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Client;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Carbon\Carbon;

class AuthController extends Controller
{


public function login(Request $request)
{
    // Validation des données de la requête
    $request->validate([
        'login' => 'required|string',
        'password' => 'required|string|min:6',
    ]);

    // Tenter de se connecter
    $credentials = ['login' => $request->login, 'password' => $request->password];

    if (!Auth::attempt($credentials)) {
        return response()->json([
            'status' => 401,
            'data' => null,
            'message' => 'Invalid credentials',
        ], 401);
    }

    // Si la connexion réussit, générer un token Passport (qui sera un JWT)
    /** @var \App\Models\User $user **/
    $user = Auth::user();

    // Créer un token Passport
    $tokenResult = $user->createToken('Personal Access Token');
    $accessToken = $tokenResult->accessToken; // Le JWT généré
    $refreshToken = $tokenResult->token->id; // Le token de rafraîchissement

    // Retourner le JWT dans la réponse
    return response()->json([
        'status' => 200,
        'data' => [
            'token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $tokenResult->token->expires_at->diffInSeconds(now()), // Temps d'expiration du token
        ],
        'message' => 'Login successful',
    ], 200);
}

// public function login(Request $request)
// {
//     // Validation des données de la requête
//     $request->validate([
//         'login' => 'required|string',
//         'password' => 'required|string|min:6',
//     ]);

//     // Tenter de se connecter avec les identifiants
//     $credentials = ['login' => $request->login, 'password' => $request->password];

//     if (!Auth::attempt($credentials)) {
//         return response()->json([
//             'status' => 401,
//             'data' => null,
//             'message' => 'Invalid credentials',
//         ], 401);
//     }

//     // Si la connexion réussit, récupérer l'utilisateur connecté
//     /** @var \App\Models\User $user **/
//     $user = Auth::user();

//     // Chiffrer les informations utilisateur que tu veux stocker dans le JWT
//     $encryptedData = Crypt::encrypt([
//         'id' => $user->id,
//         'email' => $user->email,
//         'login' => $user->login,
//     ]);

//     // Créer un JWT personnalisé avec lcobucci/jwt
//     $signer = new Sha256();
//     $key = new Key('Cybershvdow10@'); // Remplace par une clé secrète forte
//     $now = Carbon::now();

//     $token = (new Builder())
//         ->issuedBy(config('app.url')) // L'émetteur du token (ton application)
//         ->permittedFor(config('app.url')) // L'audience du token (ton application)
//         ->issuedAt($now->timestamp) // Date d'émission
//         ->expiresAt($now->addMinutes(60)->timestamp) // Date d'expiration
//         ->withClaim('encryptedData', $encryptedData) // Ajoute les données chiffrées dans le payload
//         ->getToken($signer, $key); // Générer le token signé

//     // Retourner le JWT dans la réponse
//     return response()->json([
//         'status' => 200,
//         'data' => [
//             'token' => (string) $token, // Le JWT généré
//             'token_type' => 'Bearer',
//             'expires_in' => 3600, // Temps d'expiration du token en secondes
//         ],
//         'message' => 'Login successful',
//     ], 200);
// }

    public function refreshToken(Request $request)
{
    $request->validate([
        'refresh_token' => 'required',
    ]);

    $refreshTokenId = $request->input('refresh_token');
     /** @var \App\Models\User $user **/
    $user = Auth::user();

    $userToken = $user->tokens()->where('id', $refreshTokenId)->first();

    if (!$userToken) {
        return response()->json([
            'status' => 'ECHEC',
            'message' => 'Token de rafraîchissement invalide.',
        ], 401);
    }

    // Crée un nouveau token d'accès
    $newToken = $user->createToken('Personal Access Token');
    $accessToken = $newToken->accessToken;

    return response()->json([
        'status' => 200,
        'data' => [
            'token' => $accessToken,
            'refresh_token' => $newToken->token->id,
            'token_type' => 'Bearer',
            'expires_in' => $newToken->token->expires_at->diffInSeconds(now()),
        ],
        'message' => 'Token rafraîchi avec succès.',
    ], 200);
}

const CLIENT_ROLE_ID = 3;
public function register(StoreUserRequest $request): JsonResponse
{
    // Vérifier si le rôle CLIENT (id 3) existe
    $role = Role::find(self::CLIENT_ROLE_ID);

    if (!$role) {
        return response()->json([
            'status' => 'ECHEC',
            'message' => 'Le rôle spécifié n\'existe pas.'
        ], 400);
    }

    // Vérifier si le client spécifié existe
    $client = Client::find($request->input('client_id'));

    if (!$client) {
        return response()->json([
            'status' => 'ECHEC',
            'message' => 'Le client spécifié n\'existe pas.'
        ], 400);
    }

    // Stocker le lien de la photo
    $photoUrl = null;
    if ($request->hasFile('photo')) {
        $photo = $request->file('photo');
        $photoPath = $photo->store('photos', 'public'); // Stocke la photo dans le répertoire 'storage/app/public/photos'
        $photoUrl = Storage::url($photoPath); // Récupère l'URL publique de la photo
    }

    // Créer un nouvel utilisateur avec le rôle CLIENT
    $user = User::create([
        'nom' => $request->input('nom'),
        'prenom' => $request->input('prenom'),
        'login' => $request->input('login'),
        'password' => Hash::make($request->input('password')),
        'photo' => $photoUrl,
        'roleId' => self::CLIENT_ROLE_ID, // Assigner le rôle CLIENT
    ]);

    // Mettre à jour le client avec l'ID de l'utilisateur créé
    $client->user_id = $user->id;
    $client->save();

    // Retourner une réponse JSON avec l'utilisateur créé
    return response()->json([
        'status' => 'SUCCESS',
        'data' => [
            'user' => $user,
            'client' => $client
        ],
    ], 201);
}


/**
     * Déconnecte l'utilisateur authentifié en révoquant son token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user) {
            // Révoquer le token actuel de l'utilisateur
            $request->user()->token()->revoke();

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Déconnexion réussie.'
            ]);
        }

        return response()->json([
            'status' => 'ECHEC',
            'message' => 'Aucun utilisateur authentifié.'
        ], 401);
    }

}
