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
        // Récupérer aléatoirement des articles et leurs libellés
        $articles = Article::inRandomOrder()->limit(3)->pluck('libelle')->toArray(); // Limiter à 3 articles max
        
        return [
            'date' => $this->faker->date(),
            'montant' => $this->faker->randomFloat(2, 100, 1000), // Montant entre 100 et 1000
            'montantDu' => $this->faker->randomFloat(2, 50, 500), // Montant dû entre 50 et 500
            'montantRestant' => $this->faker->randomFloat(2, 0, 500), // Montant restant entre 0 et 500
            'client_id' => Client::factory(), // Générer un client associé
            'article_libelles' => json_encode($articles), // Stocker les libellés des articles sous forme JSON
        ];
    }
}
