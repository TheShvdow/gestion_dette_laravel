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
        // Exemple de condition pour renvoyer une réponse personnalisée
        // if (!$request->has('required_param')) {
        //     return $this->sendError(
        //         null,
        //         'Paramètre requis manquant.',
        //         400
        //     );
        // }

        // // Continuer le traitement de la requête
        // $response = $next($request);

        // // Ajouter une réponse personnalisée en cas de succès
        // if ($response instanceof JsonResponse && $response->status() === 200) {
        //     $data = $response->getData(true); // Récupérer les données de la réponse
        //     return $this->sendResponse(
        //         $data['data'] ?? null,
        //         'Succès',
        //         200
        //     );
        // }

        // return $response;
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
