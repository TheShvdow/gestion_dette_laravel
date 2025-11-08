<?php

namespace App\Http\Controllers;


use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Traits\RestResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
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
     *             @OA\Property(property="login", type="string", example="idrissa.wade"),
     *             @OA\Property(property="password", type="string", example="SecureP@ss2024!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="eyJhbGci...")
     *             ),
     *             @OA\Property(property="message", type="string", example="Connexion reussie")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Erreur de validation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Échec de l'authentification",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=401),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Echec de l'authentification")
     *         )
     *     )
     * )
     */

public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 411,
                'data' => $validator->errors(),
                'message' => 'Erreur de validation'
            ], 411);
        }

        try {
            $authResult = $this->authService->authentificate($request->only('login', 'password'));
            $token = $authResult["token"];

            return response()->json([
                'status' => 200,
                'data' => ['token' => $token],
                'message' => 'Connexion reussie'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 401,
                'data' => null,
                'message' => 'Echec de l\'authentification'
            ], 401);
        }
    }
/**
 * @OA\Post(
 *     path="/register",
 *     tags={"Authentification"},
 *     summary="Créer un compte utilisateur pour un client",
 *     description="Crée un compte utilisateur pour un client existant (rôle Boutiquier requis)",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"login", "password", "clientId"},
 *             @OA\Property(property="login", type="string", example="client.user"),
 *             @OA\Property(property="password", type="string", example="SecureP@ss2024!"),
 *             @OA\Property(property="clientId", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Utilisateur créé avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=201),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="client", ref="#/components/schemas/Client"),
 *                 @OA\Property(property="user", ref="#/components/schemas/User")
 *             ),
 *             @OA\Property(property="message", type="string", example="Utilisateur cree avec succees")
 *         )
 *     ),
 *     @OA\Response(
 *         response=411,
 *         description="Erreur de validation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=411),
 *             @OA\Property(property="data", type="object"),
 *             @OA\Property(property="message", type="string", example="Erreur de validation")
 *         )
 *     )
 * )
 */
public function register(Request $request): JsonResponse
{
    // Validation
    $validator = Validator::make($request->all(), [
        'login' => 'required|string|unique:users,login',
        'password' => ['required', new \App\Rules\CustumPasswordRule()],
        'clientId' => 'required|integer|exists:clients,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 411,
            'data' => $validator->errors(),
            'message' => 'Erreur de validation'
        ], 411);
    }

    try {
        DB::beginTransaction();

        // Récupérer le client
        $client = Client::find($request->input('clientId'));

        // Vérifier que le client n'a pas déjà un compte
        if ($client->user_id) {
            return response()->json([
                'status' => 411,
                'data' => ['clientId' => ['Ce client a déjà un compte utilisateur']],
                'message' => 'Erreur de validation'
            ], 411);
        }

        // Créer l'utilisateur avec le rôle CLIENT (id 3)
        $user = User::create([
            'nom' => '',
            'prenom' => '',
            'login' => $request->input('login'),
            'password' => Hash::make($request->input('password')),
            'roleId' => self::CLIENT_ROLE_ID,
            'active' => 'oui',
        ]);

        // Associer le client au user
        $client->user_id = $user->id;
        $client->save();

        DB::commit();

        return response()->json([
            'status' => 201,
            'data' => [
                'client' => $client,
                'user' => $user
            ],
            'message' => 'Utilisateur cree avec succees'
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 500,
            'data' => null,
            'message' => $e->getMessage()
        ], 500);
    }
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
