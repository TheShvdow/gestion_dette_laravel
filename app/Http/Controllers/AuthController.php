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

    // Si la connexion réussit, générer un token Passport
    /** @var \App\Models\User $user **/
    $user = Auth::user();
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->accessToken; // Le jeton d'accès lui-même
    // dd($token);

    // Retourner le token d'accès dans la réponse
    return response()->json([
        'status' => 200,
        'data' => ['token' => $token], // Affiche le jeton d'accès
        'message' => 'Login successful',
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

// Méthode pour récupérer l'utilisateur avec la photo en base64
public function getUser($id): JsonResponse
{
    $user = User::findOrFail($id);

    // Convertir la photo en base64 si elle est présente
    $photoBase64 = null;
    if ($user->photo) {
        $photoPath = public_path($user->photo); // Obtenez le chemin absolu du fichier
        if (file_exists($photoPath)) {
            $photoBase64 = base64_encode(file_get_contents($photoPath));
        }
    }

    return response()->json([
        'status' => 'SUCCESS',
        'data' => [
            'user' => $user,
            'photo_base64' => $photoBase64,
        ],
    ]);
}




}
