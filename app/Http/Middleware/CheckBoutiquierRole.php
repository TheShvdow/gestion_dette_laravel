<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckBoutiquierRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Vérifiez si l'utilisateur a le rôle "BOUTIQUIER"
            if (Auth::user()->role && Auth::user()->role->libelle === 'Boutiquier') {
                return $next($request);
            } else {
                return response()->json(['status' => 'ECHEC', 'message' => 'Accès refusé. Vous devez être un boutiquier pour accéder à cette ressource.'], 403);
            }
        }

        return response()->json(['status' => 'ECHEC', 'message' => 'Non authentifié.'], 401);
    }
}
