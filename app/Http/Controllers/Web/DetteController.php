<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Dette;
use App\Models\Client;
use App\Models\Article;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DetteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statut = $request->input('statut');

        $dettes = Dette::query()
            ->with(['client.user'])
            ->when($search, function ($query, $search) {
                $query->whereHas('client', function ($q) use ($search) {
                    $q->where('surname', 'like', "%{$search}%")
                      ->orWhere('telephone', 'like', "%{$search}%");
                });
            })
            ->when($statut, function ($query, $statut) {
                if ($statut === 'solde') {
                    $query->where('status', 'Solde');
                } elseif ($statut === 'non_solde') {
                    $query->where('status', '!=', 'Solde');
                }
            })
            ->orderBy('date', 'desc')
            ->paginate(15)
            ->withQueryString();

        $clients = Client::with('user')->where(function($q) {
            $q->whereHas('user', function($query) {
                $query->where('active', 'oui');
            });
        })->get();

        $articles = Article::where('quantite', '>', 0)->get();

        return Inertia::render('Boutiquier/Dettes/Index', [
            'dettes' => $dettes,
            'clients' => $clients,
            'articles' => $articles,
            'filters' => [
                'search' => $search,
                'statut' => $statut,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'montant' => 'required|numeric|min:0',
            'articles' => 'required|array|min:1',
            'articles.*.article_id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.prix' => 'required|numeric|min:0',
        ], [
            'client_id.required' => 'Le client est obligatoire',
            'client_id.exists' => 'Le client sélectionné n\'existe pas',
            'montant.required' => 'Le montant est obligatoire',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être supérieur ou égal à 0',
            'articles.required' => 'Au moins un article est obligatoire',
            'articles.min' => 'Au moins un article est obligatoire',
        ]);

        DB::beginTransaction();
        try {
            // Préparer les détails des articles pour le JSON
            $articleDetails = [];

            // Vérifier les stocks et préparer les détails
            foreach ($validated['articles'] as $articleData) {
                $article = Article::findOrFail($articleData['article_id']);

                // Vérifier le stock
                if ($article->quantite < $articleData['quantite']) {
                    throw new \Exception("Stock insuffisant pour l'article: {$article->libelle}");
                }

                // Ajouter les détails de l'article
                $articleDetails[] = [
                    'article_id' => $article->id,
                    'libelle' => $article->libelle,
                    'quantite' => $articleData['quantite'],
                    'prix' => $articleData['prix'],
                    'total' => $articleData['quantite'] * $articleData['prix'],
                ];
            }

            // Créer la dette avec les détails des articles
            $dette = Dette::create([
                'client_id' => $validated['client_id'],
                'date' => now(),
                'montant' => $validated['montant'],
                'montantDu' => 0,
                'montantRestant' => $validated['montant'],
                'status' => 'NonSolde',
                'article_details' => $articleDetails,
            ]);

            // Mettre à jour les stocks
            foreach ($validated['articles'] as $articleData) {
                $article = Article::findOrFail($articleData['article_id']);
                $article->quantite -= $articleData['quantite'];
                $article->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Dette créée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Dette $dette)
    {
        $dette->load(['client.user', 'paiements' => function($q) {
            $q->orderBy('date', 'desc');
        }]);

        return Inertia::render('Boutiquier/Dettes/Show', [
            'dette' => $dette,
        ]);
    }

    public function addPaiement(Request $request, Dette $dette)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0.01|max:' . $dette->montantRestant,
        ], [
            'montant.required' => 'Le montant est obligatoire',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être supérieur à 0',
            'montant.max' => 'Le montant ne peut pas dépasser le montant restant (' . $dette->montantRestant . ' FCFA)',
        ]);

        DB::beginTransaction();
        try {
            // Créer le paiement
            Paiement::create([
                'dette_id' => $dette->id,
                'date' => now(),
                'montant' => $validated['montant'],
            ]);

            // Mettre à jour la dette
            $dette->montantDu += $validated['montant'];
            $dette->montantRestant -= $validated['montant'];

            // Mettre à jour le statut
            if ($dette->montantRestant <= 0) {
                $dette->status = 'Solde';
            }

            $dette->save();

            DB::commit();
            return redirect()->back()->with('success', 'Paiement enregistré avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement du paiement');
        }
    }

    public function destroy(Dette $dette)
    {
        // Vérifier si la dette a des paiements
        if ($dette->paiements()->exists()) {
            return redirect()->back()->with('error', 'Impossible de supprimer une dette qui a des paiements');
        }

        DB::beginTransaction();
        try {
            // Remettre les stocks des articles
            $articleDetails = $dette->article_details;
            if ($articleDetails) {
                foreach ($articleDetails as $articleDetail) {
                    $article = Article::find($articleDetail['article_id']);
                    if ($article) {
                        $article->quantite += $articleDetail['quantite'];
                        $article->save();
                    }
                }
            }

            // Supprimer la dette
            $dette->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Dette supprimée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la suppression de la dette');
        }
    }
}
