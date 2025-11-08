<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomResponseMiddleware
{
    /**
     * Gère la requête entrante et renvoie des réponses personnalisées.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Continuer le traitement de la requête
        $response = $next($request);

        // Vous pouvez ajouter une logique de réponse personnalisée ici si nécessaire

        return $response;
    }

    /**
     * Génère une réponse JSON pour le succès.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function sendResponse($data, $message = 'Opération réussie', $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'status' => 'SUCCESS',
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Génère une réponse JSON pour les erreurs.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function sendError($data, $message = 'Erreur', $statusCode = 400): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'status' => 'ERROR',
            'message' => $message,
        ], $statusCode);
    }
}
