<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use App\Enums\StateEnum;

class ClientController extends Controller
{
    use RestResponseTrait;

   /**
 * @OA\Get(
 *     path="/clients",
 *     tags={"Clients"},
 *     summary="Lister l'ensemble des clients",
 *     description="Retourne la liste des clients avec filtres optionnels: comptes=oui|non et active=oui|non. Si la liste est vide, data sera null avec message 'Pas clients'. Sinon, data contiendra un tableau de clients avec message 'Liste des clients'.",
 *     @OA\Parameter(
 *         name="comptes",
 *         in="query",
 *         description="Filtrer par présence de compte utilisateur (oui/non)",
 *         required=false,
 *         @OA\Schema(type="string", enum={"oui", "non"})
 *     ),
 *     @OA\Parameter(
 *         name="active",
 *         in="query",
 *         description="Filtrer par compte actif (oui/non)",
 *         required=false,
 *         @OA\Schema(type="string", enum={"oui", "non"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Réponse avec liste des clients (data peut être null si aucun client)",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(
 *                 property="data",
 *                 oneOf={
 *                     @OA\Schema(type="array", @OA\Items(ref="#/components/schemas/Client")),
 *                     @OA\Schema(type="null")
 *                 }
 *             ),
 *             @OA\Property(property="message", type="string", example="Liste des clients")
 *         )
 *     )
 * )
 */
    public function index(Request $request): JsonResponse
    {
        $query = Client::query();

        // Filtre comptes=oui|non
        if ($request->has('comptes')) {
            if ($request->input('comptes') === 'oui') {
                $query->whereNotNull('user_id');
            } elseif ($request->input('comptes') === 'non') {
                $query->whereNull('user_id');
            }
        }

        // Filtre active=oui|non
        if ($request->has('active')) {
            $activeValue = $request->input('active');
            $query->whereHas('user', function($q) use ($activeValue) {
                $q->where('active', $activeValue);
            });
        }

        $clients = $query->get();

        if ($clients->isEmpty()) {
            return response()->json([
                'status' => 200,
                'data' => null,
                'message' => 'Pas clients'
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'data' => $clients,
            'message' => 'Liste des clients'
        ], 200);
    }

    /**
 * @OA\Post(
 *     path="/clients",
 *     tags={"Clients"},
 *     summary="Enregistrer un client",
 *     description="Enregistre un client et éventuellement son compte utilisateur si login et password sont fournis.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/StoreClientRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Client enregistré avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=201),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="client", ref="#/components/schemas/Client"),
 *                 @OA\Property(property="user", ref="#/components/schemas/User")
 *             ),
 *             @OA\Property(property="message", type="string", example="Client enregistre avec succees")
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
public function store(StoreClientRequest $request): JsonResponse
{
    try {
        DB::beginTransaction();

        // Créer le client avec les nouveaux champs
        $client = Client::create([
            'surname' => $request->input('nom') . ' ' . $request->input('prenom'),
            'telephone' => $request->input('telephone'),
            'adresse' => null,
        ]);

        $userData = null;

        // Si login est fourni, créer un compte utilisateur
        if ($request->has('login') && $request->filled('login')) {
            $user = User::create([
                'nom' => $request->input('nom'),
                'prenom' => $request->input('prenom'),
                'login' => $request->input('login'),
                'password' => bcrypt($request->input('password')),
                'roleId' => 3, // CLIENT
                'active' => 'oui',
            ]);

            // Associer le client au user
            $client->user_id = $user->id;
            $client->save();

            $userData = $user;
        }

        DB::commit();

        $responseData = ['client' => $client];
        if ($userData) {
            $responseData['user'] = $userData;
        }

        return response()->json([
            'status' => 201,
            'data' => $responseData,
            'message' => 'Client enregistre avec succees'
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


    /**
 * @OA\Get(
 *     path="/clients/{id}",
 *     tags={"Clients"},
 *     summary="Obtenir les informations d'un client",
 *     description="Renvoie les informations d'un client sans son compte utilisateur.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="data", ref="#/components/schemas/Client"),
 *             @OA\Property(property="message", type="string", example="article trouve")
 *         )
 *     ),
 *     @OA\Response(
 *         response=411,
 *         description="Client non trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=411),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Objet non trouve")
 *         )
 *     )
 * )
 */
    public function show(string $id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouve'
            ], 411);
        }

        return response()->json([
            'status' => 200,
            'data' => $client,
            'message' => 'article trouve'
        ], 200);
    }

    /**
 * @OA\Post(
 *     path="/clients/{id}/dettes",
 *     tags={"Clients"},
 *     summary="Lister les dettes d'un client",
 *     description="Renvoie les informations du client ainsi que ses dettes (sans détails).",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="surname", type="string", example="Dupont"),
 *                 @OA\Property(property="telephone", type="string", example="771234567"),
 *                 @OA\Property(property="adresse", type="string", example="Dakar"),
 *                 @OA\Property(
 *                     property="dettes",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer"),
 *                         @OA\Property(property="date", type="string", format="date"),
 *                         @OA\Property(property="montant", type="number"),
 *                         @OA\Property(property="montantDu", type="number"),
 *                         @OA\Property(property="montantRestant", type="number"),
 *                         @OA\Property(property="status", type="string")
 *                     )
 *                 )
 *             ),
 *             @OA\Property(property="message", type="string", example="client trouve")
 *         )
 *     ),
 *     @OA\Response(
 *         response=411,
 *         description="Client non trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=411),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Objet non trouve")
 *         )
 *     )
 * )
 */
    public function getClientDettes(string $id): JsonResponse
    {
        $client = Client::with(['dettes' => function($query) {
            $query->select('id', 'date', 'montant', 'montantDu', 'montantRestant', 'client_id', 'status');
        }])->find($id);

        if (!$client) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouve'
            ], 411);
        }

        $clientData = $client->toArray();
        $clientData['dettes'] = $client->dettes->isEmpty() ? null : $client->dettes;

        return response()->json([
            'status' => 200,
            'data' => $clientData,
            'message' => 'client trouve'
        ], 200);
    }

    /**
 * @OA\Post(
 *     path="/clients/{id}/user",
 *     tags={"Clients"},
 *     summary="Afficher les informations du client avec son compte utilisateur",
 *     description="Renvoie les informations du client ainsi que son compte utilisateur.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="surname", type="string", example="Dupont"),
 *                 @OA\Property(property="telephone", type="string", example="771234567"),
 *                 @OA\Property(property="adresse", type="string", example="Dakar"),
 *                 @OA\Property(property="user", ref="#/components/schemas/User")
 *             ),
 *             @OA\Property(property="message", type="string", example="client trouve")
 *         )
 *     ),
 *     @OA\Response(
 *         response=411,
 *         description="Client non trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=411),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Objet non trouve")
 *         )
 *     )
 * )
 */
    public function getClientWithUser(string $id): JsonResponse
    {
        $client = Client::with('user')->find($id);

        if (!$client) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouve'
            ], 411);
        }

        return response()->json([
            'status' => 200,
            'data' => $client,
            'message' => 'client trouve'
        ], 200);
    }
}
