<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Dette;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Statistiques générales
            $stats = [
                'clients' => [
                    'total' => Client::count(),
                    'avec_compte' => Client::whereNotNull('user_id')->count(),
                    'sans_compte' => Client::whereNull('user_id')->count(),
                ],
                'dettes' => [
                    'total' => Dette::count(),
                    'en_cours' => Dette::where('montantRestant', '>', 0)->count(),
                    'soldees' => Dette::where('montantRestant', '=', 0)->count(),
                    'montant_total' => Dette::sum('montant'),
                    'montant_du' => Dette::sum('montantDu'),
                    'montant_restant' => Dette::sum('montantRestant'),
                ],
                'articles' => [
                    'total' => Article::count(),
                    'en_stock' => Article::where('quantite', '>', 0)->count(),
                    'rupture_stock' => Article::where('quantite', '=', 0)->count(),
                ],
            ];

            // Si c'est un Boutiquier, ajouter les statistiques utilisateurs
            if ($user->roleId == 2) { // Boutiquier
                $stats['users'] = [
                    'total' => User::count(),
                    'actifs' => User::where('active', 'oui')->count(),
                    'inactifs' => User::where('active', 'non')->count(),
                ];
            }

            // Dettes récentes (5 dernières)
            $dettes_recentes = Dette::with(['client'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($dette) {
                    return [
                        'id' => $dette->id,
                        'montant' => $dette->montant,
                        'montantRestant' => $dette->montantRestant,
                        'client' => $dette->client ? [
                            'id' => $dette->client->id,
                            'surname' => $dette->client->surname,
                            'telephone' => $dette->client->telephone,
                        ] : null,
                        'date' => $dette->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            // Données pour les graphiques
            $chartData = [
                // Dettes par mois (6 derniers mois)
                'dettesParMois' => DB::table('dettes')
                    ->select(
                        DB::raw("TO_CHAR(created_at, 'YYYY-MM') as mois"),
                        DB::raw('SUM(montant) as total')
                    )
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('mois')
                    ->orderBy('mois')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'mois' => $item->mois,
                            'total' => (float) $item->total
                        ];
                    }),

                // Paiements par mois (6 derniers mois)
                'paiementsParMois' => DB::table('dettes')
                    ->select(
                        DB::raw("TO_CHAR(created_at, 'YYYY-MM') as mois"),
                        DB::raw('SUM("montantDu") as total')
                    )
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('mois')
                    ->orderBy('mois')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'mois' => $item->mois,
                            'total' => (float) $item->total
                        ];
                    }),

                // Répartition par statut
                'dettesParStatut' => [
                    [
                        'label' => 'En cours',
                        'value' => Dette::where('montantRestant', '>', 0)->count()
                    ],
                    [
                        'label' => 'Soldées',
                        'value' => Dette::where('montantRestant', '=', 0)->count()
                    ]
                ],

                // Top 5 clients avec le plus de dettes
                'topClients' => DB::table('dettes')
                    ->select(
                        'clients.surname as name',
                        DB::raw('SUM(dettes."montantRestant") as value')
                    )
                    ->join('clients', 'dettes.client_id', '=', 'clients.id')
                    ->where('dettes.montantRestant', '>', 0)
                    ->groupBy('clients.id', 'clients.surname')
                    ->orderByDesc('value')
                    ->limit(5)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->name,
                            'value' => (float) $item->value
                        ];
                    })
            ];

            return response()->json([
                'status' => 200,
                'data' => [
                    'statistics' => $stats,
                    'recent_dettes' => $dettes_recentes,
                    'chartData' => $chartData,
                ],
                'message' => 'Dashboard data retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'data' => null,
                'message' => 'Error retrieving dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }
}
