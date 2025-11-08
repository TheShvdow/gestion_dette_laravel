<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Créer un compte utilisateur",
     *     description="Crée un compte utilisateur avec le rôle Admin ou Boutiquier",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserRequest")
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
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'nom' => '',
                'prenom' => '',
                'login' => $request->input('login'),
                'password' => bcrypt($request->input('password')),
                'roleId' => $request->input('roleId'),
                'active' => 'oui',
            ]);

            DB::commit();

            return response()->json([
                'status' => 201,
                'data' => ['user' => $user],
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

    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Lister tous les comptes utilisateurs",
     *     description="Retourne la liste des utilisateurs avec filtres optionnels: role et active",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filtrer par libellé du rôle (ADMIN, BOUTIQUIER, CLIENT)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"ADMIN", "BOUTIQUIER", "CLIENT"})
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Filtrer par statut actif (oui/non)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"oui", "non"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(type="array", @OA\Items(ref="#/components/schemas/User")),
     *                     @OA\Schema(type="null")
     *                 }
     *             ),
     *             @OA\Property(property="message", type="string", example="Liste des utilisateurs")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with('role');

        // Filtre par rôle (libellé)
        if ($request->has('role') && $request->filled('role')) {
            $roleValue = $request->input('role');
            $query->whereHas('role', function($q) use ($roleValue) {
                $q->where('libelle', $roleValue);
            });
        }

        // Filtre par active
        if ($request->has('active') && $request->filled('active')) {
            $query->where('active', $request->input('active'));
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            return response()->json([
                'status' => 200,
                'data' => null,
                'message' => 'Pas utilisateurs'
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'data' => $users,
            'message' => 'Liste des utilisateurs'
        ], 200);
    }
}
