<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'montant', 'montantDu', 'montantRestant', 'client_id', 'articles'];

    // Relation Many-to-One avec Client (Une dette appartient à un client)
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Définir une relation personnalisée pour les articles via une structure JSON
    public function getArticlesAttribute($value)
    {
        return json_decode($value, true);  // Decode la colonne articles stockée comme JSON
    }

    public function setArticlesAttribute($value)
    {
        $this->attributes['articles'] = json_encode($value);  // Stocke la valeur comme JSON
    }
    public function articles()
    {
        // Récupérer les libellés d'articles stockés dans le champ JSON
        $libelles = $this->article_libelles;

        if ($libelles) {
            // Rechercher ou créer des articles en fonction des libellés
            return Article::whereIn('libelle', $libelles)->get();
        }

        return collect([]); // Retourne une collection vide si aucun libellé n'est trouvé
    }
    public function syncArticles(array $libelles)
    {
        $this->article_libelles = json_encode($libelles); // Sauvegarder les libellés sous forme de JSON

        // Créer ou trouver les articles correspondants
        foreach ($libelles as $libelle) {
            Article::firstOrCreate(['libelle' => $libelle]); // Créer l'article si non existant
        }

        $this->save(); // Sauvegarder les modifications dans la base de données
    }

}
