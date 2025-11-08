<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'libelle' => 'Riz (sac 50kg)',
                'prix' => 25000,
                'quantite' => 50,
            ],
            [
                'libelle' => 'Huile (bidon 5L)',
                'prix' => 8500,
                'quantite' => 30,
            ],
            [
                'libelle' => 'Sucre (kg)',
                'prix' => 750,
                'quantite' => 100,
            ],
            [
                'libelle' => 'Lait en poudre (boite)',
                'prix' => 2500,
                'quantite' => 40,
            ],
            [
                'libelle' => 'Farine (kg)',
                'prix' => 650,
                'quantite' => 80,
            ],
            [
                'libelle' => 'Tomate concentree (boite)',
                'prix' => 450,
                'quantite' => 60,
            ],
            [
                'libelle' => 'Savon de lessive (paquet)',
                'prix' => 1200,
                'quantite' => 45,
            ],
            [
                'libelle' => 'Cafe (paquet 250g)',
                'prix' => 1500,
                'quantite' => 35,
            ],
            [
                'libelle' => 'The (boite)',
                'prix' => 800,
                'quantite' => 50,
            ],
            [
                'libelle' => 'Cube Maggi (boite)',
                'prix' => 300,
                'quantite' => 120,
            ],
        ];

        foreach ($articles as $article) {
            Article::create($article);
        }
    }

}
