<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Client;
use App\Models\Article;
use App\Models\Dette;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('role');
        $stats = [];
        $chartData = [];

        // Stats selon le rôle
        switch ($user->role->libelle) {
            case 'Admin':
                $stats = [
                    'users' => User::count(),
                    'clients' => Client::count(),
                    'articles' => Article::count(),
                    'dettesEnCours' => Dette::where('status', '!=', 'Solde')->count(),
                    'dettesSoldees' => Dette::where('status', 'Solde')->count(),
                    'totalDettes' => Dette::sum('montant'),
                    'totalPaye' => Dette::sum('montantDu'),
                    'totalRestant' => Dette::sum('montantRestant'),
                ];

                // Évolution des dettes par mois (6 derniers mois)
                $dettesParMois = Dette::select(
                    DB::raw('TO_CHAR(date, \'YYYY-MM\') as mois'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(montant) as total')
                )
                ->where('date', '>=', now()->subMonths(6))
                ->groupBy('mois')
                ->orderBy('mois')
                ->get();

                // Évolution des paiements par mois
                $paiementsParMois = Paiement::select(
                    DB::raw('TO_CHAR(date, \'YYYY-MM\') as mois'),
                    DB::raw('SUM(montant) as total')
                )
                ->where('date', '>=', now()->subMonths(6))
                ->groupBy('mois')
                ->orderBy('mois')
                ->get();

                $chartData = [
                    'dettesParMois' => $dettesParMois,
                    'paiementsParMois' => $paiementsParMois,
                    'dettesParStatut' => [
                        ['label' => 'En cours', 'value' => $stats['dettesEnCours']],
                        ['label' => 'Soldées', 'value' => $stats['dettesSoldees']],
                    ],
                ];
                break;

            case 'Boutiquier':
                $stats = [
                    'clients' => Client::count(),
                    'clientsActifs' => Client::whereHas('user', function($q) {
                        $q->where('active', 'oui');
                    })->count(),
                    'articles' => Article::count(),
                    'articlesDisponibles' => Article::where('quantite', '>', 0)->count(),
                    'dettesEnCours' => Dette::where('status', '!=', 'Solde')->count(),
                    'dettesSoldees' => Dette::where('status', 'Solde')->count(),
                    'totalDettes' => Dette::sum('montant'),
                    'totalPaye' => Dette::sum('montantDu'),
                    'totalRestant' => Dette::sum('montantRestant'),
                ];

                // Top 5 clients avec le plus de dettes
                $topClients = Dette::select('client_id', DB::raw('SUM("montantRestant") as total'))
                    ->where('status', '!=', 'Solde')
                    ->groupBy('client_id')
                    ->orderBy('total', 'desc')
                    ->limit(5)
                    ->with('client.user')
                    ->get()
                    ->map(function($dette) {
                        return [
                            'name' => $dette->client->surname ?? 'N/A',
                            'value' => $dette->total
                        ];
                    });

                // Évolution des dettes par mois (6 derniers mois)
                $dettesParMois = Dette::select(
                    DB::raw('TO_CHAR(date, \'YYYY-MM\') as mois'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(montant) as total')
                )
                ->where('date', '>=', now()->subMonths(6))
                ->groupBy('mois')
                ->orderBy('mois')
                ->get();

                // Évolution des paiements par mois
                $paiementsParMois = Paiement::select(
                    DB::raw('TO_CHAR(date, \'YYYY-MM\') as mois'),
                    DB::raw('SUM(montant) as total')
                )
                ->where('date', '>=', now()->subMonths(6))
                ->groupBy('mois')
                ->orderBy('mois')
                ->get();

                $chartData = [
                    'dettesParMois' => $dettesParMois,
                    'paiementsParMois' => $paiementsParMois,
                    'topClients' => $topClients,
                    'dettesParStatut' => [
                        ['label' => 'En cours', 'value' => $stats['dettesEnCours']],
                        ['label' => 'Soldées', 'value' => $stats['dettesSoldees']],
                    ],
                ];
                break;

            case 'Client':
                $client = Client::where('user_id', $user->id)->first();
                if ($client) {
                    $dettes = Dette::where('client_id', $client->id);

                    $stats = [
                        'myDettes' => $dettes->where('status', '!=', 'Solde')->count(),
                        'dettesSoldees' => $dettes->where('status', 'Solde')->count(),
                        'totalDettes' => $dettes->sum('montant'),
                        'totalPaye' => $dettes->sum('montantDu'),
                        'totalRestant' => $dettes->sum('montantRestant'),
                    ];

                    // Historique des paiements
                    $historiquesPaiements = Paiement::whereIn('dette_id',
                        Dette::where('client_id', $client->id)->pluck('id')
                    )
                    ->select(
                        DB::raw('TO_CHAR(date, \'YYYY-MM\') as mois'),
                        DB::raw('SUM(montant) as total')
                    )
                    ->where('date', '>=', now()->subMonths(6))
                    ->groupBy('mois')
                    ->orderBy('mois')
                    ->get();

                    $chartData = [
                        'paiementsParMois' => $historiquesPaiements,
                        'dettesParStatut' => [
                            ['label' => 'En cours', 'value' => $stats['myDettes']],
                            ['label' => 'Soldées', 'value' => $stats['dettesSoldees']],
                        ],
                    ];
                } else {
                    $stats = [
                        'myDettes' => 0,
                        'dettesSoldees' => 0,
                        'totalDettes' => 0,
                        'totalPaye' => 0,
                        'totalRestant' => 0,
                    ];
                    $chartData = [];
                }
                break;
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
        ]);
    }
}
