<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreArticleRequest;
use \Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('check.role:Boutiquier');
    }

    /**
     * @OA\Info(title="Article API", version="1.0")
     * 
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer"
     * )
     */

   /**
 * @OA\Get(
 *     path="/articles",
 *     tags={"Articles"},
 *     summary="Lister ensemble des articles du stock",
 *     description="Liste tous les articles avec filtre optionnel de disponibilité (rôle Boutiquier requis). Retourne 'Liste des articles' si des articles sont trouvés, sinon 'Pas Articles' avec data=null",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="disponible",
 *         in="query",
 *         description="Filtrer par disponibilité: oui (qteStock > 0) ou non (qteStock = 0)",
 *         required=false,
 *         @OA\Schema(type="string", enum={"oui", "non"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Succès - Liste des articles ou 'Pas Articles' si vide",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(
 *                 property="data",
 *                 oneOf={
 *                     @OA\Schema(type="array", @OA\Items(ref="#/components/schemas/Article")),
 *                     @OA\Schema(type="null")
 *                 }
 *             ),
 *             @OA\Property(property="message", type="string", example="Liste des articles", description="'Liste des articles' si trouvés, 'Pas Articles' si vide")
 *         )
 *     )
 * )
 */

    public function index(Request $request) : JsonResponse
    {
        $disponible = $request->query('disponible');

        if ($disponible === 'oui') {
            $articles = Article::where('quantite', '>', 0)->get();
        } elseif ($disponible === 'non') {
            $articles = Article::where('quantite', '=', 0)->get();
        } else {
            $articles = Article::all();
        }

        if ($articles->isEmpty()) {
            return response()->json([
                'status' => 200,
                'data' => null,
                'message' => 'Pas Articles'
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'data' => $articles,
            'message' => 'Liste des articles'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

     /**
 * @OA\Post(
 *     path="/articles",
 *     tags={"Articles"},
 *     summary="Enregistrer un nouvel article",
 *     description="Crée un nouvel article (rôle Boutiquier requis)",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         description="Données de l'article à créer",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/StoreArticleRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Article enregistré avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=201),
 *             @OA\Property(property="data", ref="#/components/schemas/Article"),
 *             @OA\Property(property="message", type="string", example="Article enregistrer avec succees")
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
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Mapper qteStock vers quantite pour le modèle
        $articleData = [
            'libelle' => $validated['libelle'],
            'prix' => $validated['prix'],
            'quantite' => $validated['qteStock'],
        ];

        $article = Article::create($articleData);

        return response()->json([
            'status' => 201,
            'data' => $article,
            'message' => 'Article enregistrer avec succees'
        ], 201);
    }

    /**
     * Display the specified resource.
     */

   /**
 * @OA\Get(
 *     path="/articles/{id}",
 *     tags={"Articles"},
 *     summary="Lister un article à partir de l'ID",
 *     description="Récupère les détails d'un article spécifique par son ID (rôle Boutiquier requis)",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'article à récupérer",
 *         @OA\Schema(type="integer", format="int64", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Article trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="data", ref="#/components/schemas/Article"),
 *             @OA\Property(property="message", type="string", example="article trouve")
 *         )
 *     ),
 *     @OA\Response(
 *         response=411,
 *         description="Article non trouvé",
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
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouve'
            ], 411);
        }

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'article trouve'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $articles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $articles)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $articles)
    {
        //
    }

    /**
 * @OA\Post(
 *     path="/articles/libelle",
 *     tags={"Articles"},
 *     summary="Lister un article a partir de son libelle",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"libelle"},
 *             @OA\Property(property="libelle", type="string", example="Lait Laicran 700g")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Article trouve",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="data", ref="#/components/schemas/Article"),
 *             @OA\Property(property="message", type="string", example="article trouve")
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
    public function findByLibelle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 411,
                'data' => $validator->errors(),
                'message' => 'Erreur de validation'
            ], 411);
        }

        $libelle = $request->input('libelle');
        $article = Article::where('libelle', $libelle)->first();

        if (!$article) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouve'
            ], 411);
        }

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'article trouve'
        ], 200);
    }

  /**
 * @OA\Patch(
 *     path="/articles/{id}/stock",
 *     tags={"Articles"},
 *     summary="Update stock quantity of an article",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the article to update",
 *         @OA\Schema(type="integer", format="int64", example=1)
 *     ),
 *     @OA\RequestBody(
 *         description="Stock quantity to be updated",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="quantite", type="integer", example=10)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Stock updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="data", ref="#/components/schemas/Article"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid input"),
 *     @OA\Response(response=404, description="Article not found")
 * )
 */
    /**
     * @OA\Patch(
     *     path="/articles/{id}",
     *     tags={"Articles"},
     *     summary="Mettre à jour la quantité en stock d'un article",
     *     description="Met à jour la quantité en stock d'un article spécifique (rôle Boutiquier requis)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'article",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"qteStock"},
     *             @OA\Property(property="qteStock", type="integer", example=50, description="Nouvelle quantité en stock")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quantité mise à jour avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", ref="#/components/schemas/Article"),
     *             @OA\Property(property="message", type="string", example="qte stock mis a jour")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Article non trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Objet non trouve")
     *         )
     *     )
     * )
     */
    public function updateStock(Request $request, $id): JsonResponse
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'qteStock' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 411,
                'data' => $validator->errors(),
                'message' => 'Erreur de validation'
            ], 411);
        }

        // Find the article by id
        $article = Article::find($id);

        // Check if the article exists
        if (!$article) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouve'
            ], 411);
        }

        // Update the stock quantity (set to new value, not increment)
        $article->quantite = $request->input('qteStock');
        $article->save();

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'qte stock mis a jour'
        ], 200);
    }

   /**
 * @OA\Post(
 *     path="/articles/all",
 *     tags={"Articles"},
 *     summary="Mettre à jour la quantité en stock de plusieurs articles",
 *     description="Met à jour la quantité en stock de plusieurs articles en une seule requête (rôle Boutiquier requis)",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         description="Tableau d'articles avec leurs nouvelles quantités",
 *         required=true,
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 required={"id", "qteStock"},
 *                 @OA\Property(property="id", type="integer", example=1, description="ID de l'article"),
 *                 @OA\Property(property="qteStock", type="integer", example=50, description="Nouvelle quantité en stock")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Mise à jour effectuée",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="success", type="array", @OA\Items(ref="#/components/schemas/Article"), description="Liste des articles mis à jour"),
 *                 @OA\Property(
 *                     property="error",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer"),
 *                         @OA\Property(property="message", type="string")
 *                     ),
 *                     description="Liste des articles non trouvés"
 *                 )
 *             ),
 *             @OA\Property(property="message", type="string", example="Mise a jour effectuee")
 *         )
 *     )
 * )
 */
    public function updateMultipleStocks(Request $request): JsonResponse
{
    // Initialize arrays to hold results
    $successArticles = [];
    $errorArticles = [];

    foreach ($request->all() as $item) {
        // Validate each item individually
        $validator = Validator::make($item, [
            'id' => 'required|integer',
            'qteStock' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $errorArticles[] = [
                'id' => $item['id'] ?? null,
                'message' => 'Validation echouee'
            ];
            continue;
        }

        // Attempt to find and update the article
        $article = Article::find($item['id']);

        if ($article) {
            $article->quantite = $item['qteStock'];
            $article->save();
            $successArticles[] = $article;
        } else {
            $errorArticles[] = [
                'id' => $item['id'],
                'message' => 'Article non trouve'
            ];
        }
    }

    // Prepare the response data
    return response()->json([
        'status' => 200,
        'data' => [
            'success' => $successArticles,
            'error' => $errorArticles
        ],
        'message' => 'Mise a jour effectuee'
    ], 200);
}

/**
 * @OA\Delete(
 *     path="/articles/{id}",
 *     tags={"Articles"},
 *     summary="Delete an article by ID",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the article to delete",
 *         @OA\Schema(type="integer", format="int64", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Article deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Article not found")
 * )
 */
public function deleteArticle($id)
{
    $article = Article::find($id);

    if (!$article) {
        return response()->json([
            'status' => 404,
            'data' => null,
            'message' => 'Article non trouvé'
        ], 404);
    }

    $article->delete();

    return response()->json([
        'status' => 200,
        'data' => null,
        'message' => 'Article supprimé avec succès'
    ]);
}

/**
 * @OA\Patch(
 *     path="/articles/restore/{id}",
 *     tags={"Articles"},
 *     summary="Restore a soft-deleted article",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the article to restore",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Article restored",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="data", ref="#/components/schemas/Article"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Article not found")
 * )
 */
public function restoreArticle($id)
{
    $article = Article::withTrashed()->find($id);

    if (!$article) {
        return response()->json([
            'status' => 404,
            'data' => null,
            'message' => 'Article non trouvé'
        ], 404);
    }

    $article->restore();

    return response()->json([
        'status' => 200,
        'data' => $article,
        'message' => 'Article restauré avec succès'
    ]);
}



}