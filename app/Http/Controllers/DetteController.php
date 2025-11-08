<?php

namespace App\Http\Controllers;

use App\Models\Dette;
use Illuminate\Http\Request;
use App\Services\DetteService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreDetteRequest;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Dette",
 *     description="Operation relative aux dettes",
 * )
 */
class DetteController extends Controller
{

    protected $detteService;
    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
    }
    /**
     * @OA\Get(
     *     path="/dettes",
     *     tags={"Dettes"},
     *     summary="Lister ensemble des dettes",
     *     description="Liste toutes les dettes avec filtre optionnel de statut. Solde (montantRestant=0) ou NonSolde (montantRestant!=0). Retourne les dettes sans articles + client. (Rôle Boutiquier requis)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="statut",
     *         in="query",
     *         description="Filtrer par statut: Solde (montantRestant=0) ou NonSolde (montantRestant!=0)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"Solde", "NonSolde"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succes - Liste des dettes ou Pas Dettes si vide",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(type="array", @OA\Items(ref="#/components/schemas/Dette")),
     *                     @OA\Schema(type="null")
     *                 }
     *             ),
     *             @OA\Property(property="message", type="string", example="Liste des dettes", description="'Liste des dettes' si trouvees, 'Pas Dettes' si vide")
     *         )
     *     )
     * )
     */
    public function index(Request $request):JsonResponse
    {
        // Récupérer le paramètre 'statut' de la requête
        $statut = $request->query('statut');

        // Vérifier si le paramètre 'statut' est passé
        if ($statut) {
            $statuts = explode('|', $statut);
            $debts = $this->detteService->state($statuts); // Filtrer par statut
        } else {
            $debts = $this->detteService->all(); // Récupérer toutes les dettes
        }

        // Vérifier si la collection est vide
        if ($debts->isEmpty()) {
            return response()->json([
                'status' => 200,
                'data' => null,
                'message' => 'Pas Dettes'
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'data' => $debts,
            'message' => 'Liste des dettes'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
    * @OA\Post(
    *     path="/dettes",
    *     tags={"Dettes"},
    *     summary="Enregistrer une nouvelle dette",
    *     description="Crée une nouvelle dette avec articles et paiement optionnel. Met à jour le stock des articles. (Rôle Boutiquier requis)",
    *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
    *        required=true,
    *        @OA\JsonContent(ref="#/components/schemas/StoreDetteRequest")
    *     ),
    *     @OA\Response(
    *         response=201,
    *         description="Dette enregistree avec succees",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="status", type="integer", example=201),
    *             @OA\Property(property="data", ref="#/components/schemas/Dette"),
    *             @OA\Property(property="message", type="string", example="Dette enregistree avec succees")
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

    public function store(StoreDetteRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();

            // Préparer les détails des articles
            $articleDetails = [];
            foreach ($validatedData['articles'] as $articleData) {
                $article = \App\Models\Article::find($articleData['articleId']);

                // Mettre à jour le stock
                $article->quantite -= $articleData['qteVente'];
                $article->save();

                // Ajouter aux détails
                $articleDetails[] = [
                    'articleId' => $article->id,
                    'libelle' => $article->libelle,
                    'qteVente' => $articleData['qteVente'],
                    'prixVente' => $articleData['prixVente'],
                ];
            }

            // Calculer montantDu et montantRestant
            $montantDu = 0;
            if (isset($validatedData['paiement']) && isset($validatedData['paiement']['montant'])) {
                $montantDu = $validatedData['paiement']['montant'];
            }
            $montantRestant = $validatedData['montant'] - $montantDu;

            // Créer la dette
            $dette = Dette::create([
                'date' => now()->toDateString(),
                'montant' => $validatedData['montant'],
                'montantDu' => $montantDu,
                'montantRestant' => $montantRestant,
                'client_id' => $validatedData['clientId'],
                'article_details' => $articleDetails,
            ]);

            // Créer le paiement si fourni
            if (isset($validatedData['paiement']) && isset($validatedData['paiement']['montant'])) {
                \App\Models\Paiement::create([
                    'montant' => $validatedData['paiement']['montant'],
                    'date' => now()->toDateString(),
                    'dette_id' => $dette->id,
                ]);
            }

            // Charger les relations pour la réponse
            $dette->load(['client', 'paiements']);

            DB::commit();

            return response()->json([
                'status' => 201,
                'data' => $dette,
                'message' => 'Dette enregistree avec succees'
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
     * Display the specified resource.
     */


    /**
    * @OA\Get(
    *     path="/dettes/{id}",
    *     tags={"Dettes"},
    *     summary="Lister une dette a partir de l'id",
    *     description="Récupère les détails d'une dette spécifique avec le client (sans articles ni paiements)",
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="ID de la dette à récupérer",
    *         required=true,
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Dette trouvee",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="status", type="integer", example=200),
    *             @OA\Property(property="data", ref="#/components/schemas/Dette"),
    *             @OA\Property(property="message", type="string", example="Dette trouvee")
    *         )
    *     ),
    *     @OA\Response(
    *         response=411,
    *         description="Objet non trouve",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="status", type="integer", example=411),
    *             @OA\Property(property="data", type="null"),
    *             @OA\Property(property="message", type="string", example="Objet non trouve")
    *         )
    *     )
    * )
    */


    public function show(int $id): JsonResponse
    {
        try {
            $dette = $this->detteService->find($id);

            return response()->json([
                'status' => 200,
                'data' => $dette,
                'message' => 'Dette trouvee'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouve'
            ], 411);
        }
    }

    /**
     * @OA\Post(
     *     path="/dettes/{id}/articles",
     *     tags={"Dettes"},
     *     summary="Lister les articles d'une dette",
     *     description="Récupère une dette avec ses articles détaillés (Rôles: Boutiquier et Client)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la dette",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dette avec articles trouvee",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", ref="#/components/schemas/Dette"),
     *             @OA\Property(property="message", type="string", example="Dette avec articles trouvee")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Objet non trouve",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Objet non trouve")
     *         )
     *     )
     * )
     */
    public function getArticles(int $id): JsonResponse
    {
        try {
            $dette = Dette::with('client:id,surname,telephone')
                ->select('id', 'date', 'montant', 'montantDu', 'montantRestant', 'client_id', 'article_details', 'status')
                ->findOrFail($id);

            return response()->json([
                'status' => 200,
                'data' => $dette,
                'message' => 'Dette avec articles trouvee'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouve'
            ], 411);
        }
    }

    /**
     * @OA\Get(
     *     path="/dettes/{id}/paiements",
     *     tags={"Dettes"},
     *     summary="Lister les paiements d'une dette",
     *     description="Récupère une dette avec ses paiements (Rôles: Boutiquier et Client)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la dette",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dette avec paiements trouvee",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", ref="#/components/schemas/Dette"),
     *             @OA\Property(property="message", type="string", example="Dette avec paiements trouvee")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Objet non trouve",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Objet non trouve")
     *         )
     *     )
     * )
     */
    public function listPaiements(int $id): JsonResponse
    {
        try {
            $dette = Dette::with(['client:id,surname,telephone', 'paiements'])
                ->select('id', 'date', 'montant', 'montantDu', 'montantRestant', 'client_id', 'status')
                ->findOrFail($id);

            return response()->json([
                'status' => 200,
                'data' => $dette,
                'message' => 'Dette avec paiements trouvee'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouve'
            ], 411);
        }
    }

    /**
     * @OA\Post(
     *     path="/dettes/{id}/paiement",
     *     tags={"Dettes"},
     *     summary="Ajouter un paiement a une dette",
     *     description="Ajoute un paiement à une dette existante et met à jour les montants (Rôle Boutiquier requis)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la dette",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"montant"},
     *             @OA\Property(property="montant", type="number", format="float", example=50000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paiement ajoute",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", ref="#/components/schemas/Dette"),
     *             @OA\Property(property="message", type="string", example="Paiement ajoute")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Erreur de validation ou dette non trouvee",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Erreur de validation")
     *         )
     *     )
     * )
     */
    public function addPaiement(\App\Http\Requests\StorePaiementRequest $request, int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $dette = Dette::findOrFail($id);
            $montantPaiement = $request->input('montant');

            // Créer le paiement
            \App\Models\Paiement::create([
                'montant' => $montantPaiement,
                'date' => now()->toDateString(),
                'dette_id' => $dette->id,
            ]);

            // Mettre à jour la dette
            $dette->montantDu += $montantPaiement;
            $dette->montantRestant -= $montantPaiement;
            $dette->save();

            // Charger les relations pour la réponse
            $dette->load(['client:id,surname,telephone', 'paiements']);

            DB::commit();

            return response()->json([
                'status' => 200,
                'data' => $dette,
                'message' => 'Paiement ajoute'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouve'
            ], 411);
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
     * Show the form for editing the specified resource.
     */
    public function edit(Dette $dette)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dette $dette)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dette $dette)
    {
        //
    }
    public function calculateRemainingAmount(Dette $dette): float
    {
        return $dette->montant - $dette->montantDu;
    }
    public function isSettled(Dette $dette): bool
    {
        return $dette->montantRestant <= 0;
    }
    public function markAsSettled(Dette $dette): void
    {
        $dette->montantRestant = 0;
        $dette->save();
    }
    public function updateRemainingAmount(Dette $dette, float $amountPaid): void
    {
        $dette->montantDu += $amountPaid;
        $dette->montantRestant = $this->calculateRemainingAmount($dette);
        $dette->save();
    }

    
}
