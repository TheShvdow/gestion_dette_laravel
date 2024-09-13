<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Lcobucci\JWT\Builder;
use Illuminate\Http\Request;
use Lcobucci\JWT\Signer\Key;
use App\Traits\RestResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\Interfaces\AuthentificationServiceInterface;
use App\Enums\StateEnum;
use App\Services\Interfaces\FileStorageServiceInterface;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    const CLIENT_ROLE_ID = 3;
    use RestResponseTrait;  
    protected $authService;

    public function __construct(AuthentificationServiceInterface $authService){
        $this->authService = $authService;
    }


    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Authentification d'un utilisateur",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login", "password"},
     *             @OA\Property(property="login", type="string", example="donnelly.isabel"),
     *             @OA\Property(property="password", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJhbGci..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Échec de l'authentification",
     *     )
     * )
     */

public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) 
            return $this->sendResponse(['errors' => $validator->errors()], StateEnum::ECHEC, "Donnée erroné'", 422);
        try {
            $token = $this->authService->authentificate($request->only('login', 'password'))["token"];
            $refreshToken = $this->authService->authentificate($request->only('login', 'password'))["refresh_token"];
            
            
            
               
            return $this->sendResponse(['token' => $token, 'token_type' => 'Bearer','refresh_token' => $refreshToken ], message: "Connexion réussie");
        } catch (\Exception $e) {
            return $this->sendResponse(null, StateEnum::ECHEC, "Échec de l'authentification'", 401);
        }
    }
/**
 * @OA\Post(
 *     path="/register",
 *     tags={"Authentification"},
 *     summary="Register a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"nom", "prenom", "login", "password", "password_confirmation", "client_id", "roleId"},
 *                 @OA\Property(property="nom", type="string", example="Doe"),
 *                 @OA\Property(property="prenom", type="string", example="John"),
 *                 @OA\Property(property="login", type="string", example="johndoe"),
 *                 @OA\Property(property="password", type="string", format="password", example="securepassword123"),
 *                 @OA\Property(property="password_confirmation", type="string", format="password", example="securepassword123"),
 *                 @OA\Property(property="client_id", type="string", example="12345"),
 *                 @OA\Property(property="roleId", type="integer", example=1),
 *                 @OA\Property(property="photo", type="string", format="binary"),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="user", ref="#/components/schemas/User"),
 *                 @OA\Property(property="token", type="string")
 *             ),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid input"),
 *     @OA\Response(response=409, description="User already exists")
 * )
 */


 public function register(StoreUserRequest $request, FileStorageServiceInterface $fileStorageService): JsonResponse
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
        $photoUrl = $fileStorageService->upload($photo, 'photos');
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


//  public function register(StoreUserRequest $request): JsonResponse
//  {
//      // Vérifier si le rôle CLIENT (id 3) existe
//      $role = Role::find(self::CLIENT_ROLE_ID);
//      if (!$role) {
//          return response()->json([
//              'status' => 'ECHEC',
//              'message' => 'Le rôle spécifié n\'existe pas.'
//          ], 400);
//      }

//      // Vérifier si le client spécifié existe
//      $client = Client::find($request->input('client_id'));
//      if (!$client) {
//          return response()->json([
//              'status' => 'ECHEC',
//              'message' => 'Le client spécifié n\'existe pas.'
//          ], 400);
//      }

//      // Stocker le lien de la photo si elle est fournie
//      $photoUrl = null;
//      if ($request->hasFile('photo')) {
//          $photo = $request->file('photo');
//          $photoPath = $photo->store('photos', 'public'); // Stocke la photo dans le répertoire 'storage/app/public/photos'
//          $photoUrl = Storage::url($photoPath); // Récupère l'URL publique de la photo
//      }
//         //dd($request->all());
//      // Créer un nouvel utilisateur avec le rôle CLIENT
//      $user = User::create([
//          'nom' => $request->input('nom'),
//          'prenom' => $request->input('prenom'),
//          'login' => $request->input('login'),
//          'password' => Hash::make($request->input('password')??"password01234@"),
//          'photo' => $photoUrl,
//          'roleId' => self::CLIENT_ROLE_ID, // Assigner le rôle CLIENT
//      ]);

//      // Mettre à jour le client avec l'ID de l'utilisateur créé
//      $client->user_id = $user->id;
//      $client->save();

//      // Retourner une réponse JSON avec l'utilisateur créé
//      return response()->json([
//          'status' => 'SUCCESS',
//          'data' => [
//              'client' => $client,
//              'user' => $user
//          ],
//      ], 201);
//  }


/**
     * Déconnecte l'utilisateur authentifié en révoquant son token.
     *
     * @param Request $request
     * @return JsonResponse
     */


    /**
     * @OA\Post(
     *     path="/logout",
     *     tags={"Users"},
     *     summary="Déconnexion de l'utilisateur",
     *     tags={"Authentification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de la déconnexion"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request->user());
            return $this->sendResponse(null, message: "Déconnexion réussie");
        } catch (\Exception $e) {
            return $this->sendResponse(null, StateEnum::ECHEC, "Erreur lors de la déconnexion", 500);
        }
    }

    public function refreshToken(Request $request) {
        // Vérifier si le refresh token est fourni
        $refreshToken = $request->input('refresh_token');
        if (!$refreshToken) {
            return response()->json(['error' => 'Le refresh token est manquant.'], 400);
        }
    
        // Rechercher l'utilisateur avec ce refresh token
        $user = User::where('refresh_token', hash('sha256', $refreshToken))->first();
    
        if (!$user) {
            return response()->json(['error' => 'Refresh token invalide.'], 401);
        }
    
        // Générer un nouveau access token
        $accessToken = $user->createToken('auth_token')->accessToken;
    
        // Générer un nouveau refresh token
        $newRefreshToken = Str::random(60);
        $user->refresh_token = hash('sha256', $newRefreshToken);
        $user->save();
    
        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer'
        ]);
    }
    

}
