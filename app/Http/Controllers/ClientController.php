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
 *     summary="Get a list of clients",
 *     description="Retourne une liste de clients qui ont un utilisateur lié",
 *     @OA\Response(
 *         response=200,
 *         description="Liste des clients retournée avec succès",
 *         @OA\JsonContent(ref="#/components/schemas/ClientCollection")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête invalide"
 *     )
 * )
 */
    public function index(Request $request)
    {
        $include = $request->has('include') ? [$request->input('include')] : [];
        $data = Client::with($include)->whereNotNull('user_id')->get();
        $clients = QueryBuilder::for(Client::class)
            ->allowedFilters(['surname'])
            ->allowedIncludes(['user'])
            ->get();
        return new ClientCollection($clients);
    }

    /**
 * @OA\Post(
 *     path="/clients",
 *     tags={"Clients"},
 *     summary="Créer un nouveau client",
 *     description="Crée un nouveau client avec un utilisateur lié.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/StoreClientRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Client créé avec succès",
 *         @OA\JsonContent(ref="#/components/schemas/ClientResource")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides"
 *     )
 * )
 */
public function store(StoreClientRequest $request)
{
    try {
        DB::beginTransaction();
        
        $clientRequest = $request->only('surname', 'address', 'telephone');
        $client = Client::create($clientRequest);
        
        if ($request->has('user')) {
            $user = User::create([
                'nom' => $request->input('user.nom'),
                'prenom' => $request->input('user.prenom'),
                'login' => $request->input('user.login'),
                'password' => bcrypt($request->input('user.password')), // Assurez-vous que le mot de passe est haché
                'role' => $request->input('user.role'),
            ]);

            $user->client()->save($client);
        }
        
        DB::commit();
        return $this->sendResponse(new ClientResource($client),);
    } catch (\Exception $e) {
        DB::rollBack();
        return $this->sendError($e->getMessage(), $e->getCode());
    }
}


    /**
 * @OA\Get(
 *     path="/clients/{id}",
 *     tags={"Clients"},
 *     summary="Obtenir les détails d'un utilisateur",
 *     description="Renvoie les informations détaillées d'un utilisateur spécifique.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Utilisateur trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 example="SUCCESS"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="user", ref="#/components/schemas/User"),
 *                 @OA\Property(
 *                     property="photo_base64",
 *                     type="string",
 *                     example="base64encodedphoto"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Utilisateur non trouvé"
 *     )
 * )
 */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

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
