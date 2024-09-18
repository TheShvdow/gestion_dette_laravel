<?php

namespace Database\Factories;

use App\Models\Dette;
use App\Models\Client;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dette>
 */
class DetteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Dette::class;

    public function definition()
    {
        // Récupérer aléatoirement des articles et générer leurs détails
        $articles = Article::inRandomOrder()->limit(3)->get()->map(function ($article) {
            return [
                'libelle' => $article->libelle,
                'quantite' => $this->faker->numberBetween(1, 10),
                'prixVente' => $article->prix, // Utilise le prix de l'article
            ];
        });

        return [
            'date' => $this->faker->date(),
            'montant' => $this->faker->randomFloat(2, 100, 1000),
            'montantDu' => $this->faker->randomFloat(2, 50, 500),
            'montantRestant' => $this->faker->randomFloat(2, 0, 500),
            'client_id' => Client::factory(),
            'article_details' => $articles->toArray(), // Stocker les détails sous forme JSON
        ];
    }
}
