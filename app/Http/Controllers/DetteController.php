<?php

namespace App\Http\Controllers;

use App\Models\Dette;
use Illuminate\Http\Request;
use App\Services\DetteService;
use Illuminate\Http\JsonResponse;

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
     * Display a listing of the resource.
     */

   /**
 * @OA\Post(
 *     path="/dettes",
 *     tags={"Dettes"},
 *     summary="Get list of debts",
 *     description="Returns a list of debts. Optionally filter by status (Solde, NonSolde)",
 *     @OA\Parameter(
 *         name="statut",
 *         in="query",
 *         description="Filter debts by status. Use 'Solde' for fully paid debts and 'NonSolde' for unpaid debts.",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             example="Solde|NonSolde"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of debts retrieved successfully",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Dette")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request"
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

    return response()->json([
        'status' => 200,
        'data' => $debts,
        'message' => 'List of debts retrieved successfully'
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
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(Dette $dette)
    {
        
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

    
}
