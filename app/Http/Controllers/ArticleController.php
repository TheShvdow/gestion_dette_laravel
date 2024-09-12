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
 *     summary="List all articles",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of items per page",
 *         @OA\Schema(type="integer", default=10)
 *     ),
 *     @OA\Parameter(
 *         name="disponible",
 *         in="query",
 *         description="Filter by availability",
 *         @OA\Schema(type="string", enum={"oui", "non"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of articles",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Article")
 *             ),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(response=404, description="No articles found")
 * )
 */

    public function index(Request $request) : JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $articles = Article::paginate($perPage);

        $disponible = $request->query('disponible');

        if ($disponible === 'oui') {
            $articles = Article::where('quantite', '>', 0)->get();
        } elseif ($disponible === 'non') {
            $articles = Article::where('quantite', '=', 0)->get();
        } else {
            // If no filter is provided, return all articles
            $articles = Article::all();
        }

        return response()->json([
            'status' => 200,
            'data' => $articles,
            'message' => 'Articles trouves avec succès'
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
 *     summary="Store a newly created article",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         description="Article object that needs to be added",
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="libelle", type="string"),
 *             @OA\Property(property="prix", type="number", format="float"),
 *             @OA\Property(property="quantite", type="integer", example=100)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Article created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="data", ref="#/components/schemas/Article"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid input")
 * )
 */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        // Use $validated to store the validated data in your database
        
        $article = Article::create($validated);
        

        return response()->json([
            'status' => 201,
            'data' => $article,
            'message' => 'Article enregistré avec succès'
        ], 201);
    }

    /**
     * Display the specified resource.
     */

   /**
 * @OA\Get(
 *     path="/articles/{id}",
 *     tags={"Articles"},
 *     summary="Get an article by ID",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the article to retrieve",
 *         @OA\Schema(type="integer", format="int64", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Article details",
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
    public function show(int $id)
    {
        $article = Article::find($id);
        $article = Article::paginate(10);

        if (!$article) {
            return response()->json([
                'status' => 404,
                'data' => null,
                'message' => 'Article non trouve'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'Article détails récupéré avec succés'
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
 * @OA\Get(
 *     path="/articles/findByLibelle",
 *     tags={"Articles"},
 *     summary="Find an article by its libelle",
 *     @OA\Parameter(
 *         name="libelle",
 *         in="query",
 *         description="The libelle of the article to find",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Article found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="data", ref="#/components/schemas/Article"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Article not found"),
 *     @OA\Response(response=400, description="Bad Request")
 * )
 */
    public function findByLibelle(Request $request): JsonResponse
    {
        $request->validate([
            'libelle' => 'required|string',
        ]);

        $libelle = $request->input('libelle');
        $article = Article::where('libelle', $libelle)->first();

        if (!$article) {
            return response()->json([
                'status' => 404,
                'data' => null,
                'message' => 'Article non trouvé'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'Article trouvé avec succès'
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
    public function updateStock(Request $request, $id): JsonResponse
    {
        // Validate the incoming request
        $request->validate([
            'quantite' => 'required|integer|min:0',
        ]);

        // Find the article by id
        $article = Article::find($id);

        // Check if the article exists
        if (!$article) {
            return response()->json([
                'status' => 404,
                'data' => null,
                'message' => 'Article not found'
            ], 404);
        }

        // Update the stock quantity
        $article->quantite += $request->input('quantite');
        $article->save();

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'Article stock updated successfully'
        ], 200);
    }

   /**
 * @OA\Patch(
 *     path="/articles/stocks",
 *     tags={"Articles"},
 *     summary="Update stock quantities for multiple articles",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         description="List of articles with stock updates",
 *         required=true,
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="quantite", type="integer", example=10)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Stocks updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="updated", type="array", @OA\Items(ref="#/components/schemas/Article")),
 *                 @OA\Property(property="failed", type="array", @OA\Items(type="object"))
 *             ),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Validation errors"),
 *     @OA\Response(response=404, description="No articles updated")
 * )
 */
    public function updateMultipleStocks(Request $request): JsonResponse
{
    // Initialize arrays to hold results
    $updatedArticles = [];
    $failedArticles = [];
    $errors = [];

    foreach ($request->all() as $item) {
        // Validate each item individually
        $validator = Validator::make($item, [
            'id' => 'required|integer|exists:articles,id',
            'quantite' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            // If validation fails, add the errors to the errors array
            $errors[$item['id']] = $validator->errors()->all();
            continue;
        }

        // If validation passes, attempt to update the article
        $article = Article::find($item['id']);

        if ($article) {
            $article->quantite += $item['quantite'];
            $article->save();
            $updatedArticles[] = $article;
        } else {
            $failedArticles[] = $item;
        }
    }

    // Paginate the successfully updated articles
    $paginatedUpdatedArticles = (new \Illuminate\Pagination\LengthAwarePaginator(
        $updatedArticles,
        count($updatedArticles),
        10,
        $request->input('page', 1),
        ['path' => $request->url(), 'query' => $request->query()]
    ));

    // Prepare the response data
    $response = [
        'status' => 200,
        'data' => [
            'updated' => $paginatedUpdatedArticles,
            'failed' => $failedArticles
        ],
        'message' => 'Les stocks des articles ont été mis à jour avec succès pour les articles valides.'
    ];

    // If there are validation errors, update the response status and message
    if (!empty($errors)) {
        $response['status'] = 400;
        $response['message'] = 'Certaines erreurs ont été trouvées.';
        $response['errors'] = $errors;
    } elseif (empty($updatedArticles)) {
        $response['status'] = 404;
        $response['message'] = 'Aucun article mis à jour. Veuillez vérifier les identifiants.';
    }

    return response()->json($response, $response['status']);
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