<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema (
 *     schema="Dette",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         format="date-time",
 *         example="2024-01-01T00:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="montant",
 *         type="number",
 *         format="float",
 *         example=29.99
 *     ),
 *     @OA\Property(
 *         property="montantDu",
 *         type="number",
 *         format="float",
 *         example=22.99
 *     ),
 *     @OA\Property(
 *         property="montantRestant",
 *         type="number",
 *         format="float",
 *         example=7.99
 *     ),
 *     @OA\Property(
 *         property="client_id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="article_details",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", format="int64", example=1),
 *             @OA\Property(property="article_id", type="integer", format="int64", example=1),
 *             @OA\Property(property="libelle", type="string", example="Article Example"),
 *             @OA\Property(property="quantite", type="integer", format="int64", example=1),
 *             @OA\Property(property="prixVente", type="number", format="float", example=29.99)
 *         )
 *     )
 * )
 * @mixin IdeHelperDette
 */

class Dette extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'montant',
        'montantDu',
        'montantRestant',
        'client_id',
        'article_details',
        'status',
    ];

    // Relation Many-to-One avec Client (Une dette appartient à un client)
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relation One-to-Many avec Paiement (Une dette peut avoir plusieurs paiements)
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    protected $casts = [
        'article_details' => 'array', // Convertit le JSON en tableau PHP
    ];

    /**
     * Boot method to add model event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Événement avant la création d'une dette
        static::creating(function ($dette) {
            $dette->updateStatusBasedOnMontantRestant();
        });

        // Événement avant la mise à jour d'une dette
        static::updating(function ($dette) {
            $dette->updateStatusBasedOnMontantRestant();
        });
    }

    /**
     * Met à jour automatiquement le statut en fonction du montant restant
     */
    protected function updateStatusBasedOnMontantRestant()
    {
        if ($this->montantRestant == 0) {
            $this->status = \App\Enums\StatusDetteEnum::SOLDE;
        } else {
            $this->status = \App\Enums\StatusDetteEnum::NON_SOLDE;
        }
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
