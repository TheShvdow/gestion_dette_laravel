<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $disponible = $request->input('disponible');

        $articles = Article::query()
            ->when($search, function ($query, $search) {
                $query->where('libelle', 'like', "%{$search}%");
            })
            ->when($disponible === 'oui', function ($query) {
                $query->where('quantite', '>', 0);
            })
            ->when($disponible === 'non', function ($query) {
                $query->where('quantite', '<=', 0);
            })
            ->orderBy('libelle')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Boutiquier/Articles/Index', [
            'articles' => $articles,
            'filters' => [
                'search' => $search,
                'disponible' => $disponible,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255|unique:articles,libelle',
            'prix' => 'required|numeric|min:0',
            'quantite' => 'required|integer|min:0',
        ], [
            'libelle.required' => 'Le libellé est obligatoire',
            'libelle.unique' => 'Un article avec ce libellé existe déjà',
            'prix.required' => 'Le prix est obligatoire',
            'prix.numeric' => 'Le prix doit être un nombre',
            'prix.min' => 'Le prix doit être supérieur ou égal à 0',
            'quantite.required' => 'La quantité est obligatoire',
            'quantite.integer' => 'La quantité doit être un nombre entier',
            'quantite.min' => 'La quantité doit être supérieure ou égale à 0',
        ]);

        Article::create($validated);

        return redirect()->back()->with('success', 'Article créé avec succès');
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255|unique:articles,libelle,' . $article->id,
            'prix' => 'required|numeric|min:0',
            'quantite' => 'required|integer|min:0',
        ], [
            'libelle.required' => 'Le libellé est obligatoire',
            'libelle.unique' => 'Un article avec ce libellé existe déjà',
            'prix.required' => 'Le prix est obligatoire',
            'prix.numeric' => 'Le prix doit être un nombre',
            'prix.min' => 'Le prix doit être supérieur ou égal à 0',
            'quantite.required' => 'La quantité est obligatoire',
            'quantite.integer' => 'La quantité doit être un nombre entier',
            'quantite.min' => 'La quantité doit être supérieure ou égale à 0',
        ]);

        $article->update($validated);

        return redirect()->back()->with('success', 'Article mis à jour avec succès');
    }

    public function destroy(Article $article)
    {
        // Vérifier si l'article est utilisé dans des dettes
        if ($article->dettes()->exists()) {
            return redirect()->back()->with('error', 'Impossible de supprimer cet article car il est utilisé dans des dettes');
        }

        $article->delete();

        return redirect()->back()->with('success', 'Article supprimé avec succès');
    }

    public function updateStock(Request $request, Article $article)
    {
        $validated = $request->validate([
            'quantite' => 'required|integer|min:0',
            'action' => 'required|in:add,set',
        ]);

        if ($validated['action'] === 'add') {
            $article->quantite += $validated['quantite'];
        } else {
            $article->quantite = $validated['quantite'];
        }

        $article->save();

        return redirect()->back()->with('success', 'Stock mis à jour avec succès');
    }
}
