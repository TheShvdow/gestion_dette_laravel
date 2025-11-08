<?php

namespace Database\Seeders;

use App\Models\Dette;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DettesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dettes = [
            [
                'date' => now()->subDays(30)->toDateString(),
                'montant' => 125000,
                'montantDu' => 50000,
                'montantRestant' => 75000,
                'client_id' => 1,
                'status' => 'NonSolde',
                'article_details' => json_encode([
                    [
                        'article_id' => 1,
                        'libelle' => 'Riz (sac 50kg)',
                        'quantite' => 3,
                        'prixVente' => 25000,
                    ],
                    [
                        'article_id' => 2,
                        'libelle' => 'Huile (bidon 5L)',
                        'quantite' => 5,
                        'prixVente' => 8500,
                    ],
                    [
                        'article_id' => 3,
                        'libelle' => 'Sucre (kg)',
                        'quantite' => 10,
                        'prixVente' => 750,
                    ],
                ]),
            ],
            [
                'date' => now()->subDays(20)->toDateString(),
                'montant' => 85000,
                'montantDu' => 85000,
                'montantRestant' => 0,
                'client_id' => 2,
                'status' => 'Solde',
                'article_details' => json_encode([
                    [
                        'article_id' => 4,
                        'libelle' => 'Lait en poudre (boite)',
                        'quantite' => 10,
                        'prixVente' => 2500,
                    ],
                    [
                        'article_id' => 7,
                        'libelle' => 'Savon de lessive (paquet)',
                        'quantite' => 50,
                        'prixVente' => 1200,
                    ],
                ]),
            ],
            [
                'date' => now()->subDays(15)->toDateString(),
                'montant' => 45500,
                'montantDu' => 20000,
                'montantRestant' => 25500,
                'client_id' => 3,
                'status' => 'NonSolde',
                'article_details' => json_encode([
                    [
                        'article_id' => 5,
                        'libelle' => 'Farine (kg)',
                        'quantite' => 20,
                        'prixVente' => 650,
                    ],
                    [
                        'article_id' => 8,
                        'libelle' => 'Cafe (paquet 250g)',
                        'quantite' => 10,
                        'prixVente' => 1500,
                    ],
                    [
                        'article_id' => 9,
                        'libelle' => 'The (boite)',
                        'quantite' => 20,
                        'prixVente' => 800,
                    ],
                ]),
            ],
            [
                'date' => now()->subDays(10)->toDateString(),
                'montant' => 60000,
                'montantDu' => 30000,
                'montantRestant' => 30000,
                'client_id' => 1,
                'status' => 'NonSolde',
                'article_details' => json_encode([
                    [
                        'article_id' => 1,
                        'libelle' => 'Riz (sac 50kg)',
                        'quantite' => 2,
                        'prixVente' => 25000,
                    ],
                    [
                        'article_id' => 10,
                        'libelle' => 'Cube Maggi (boite)',
                        'quantite' => 20,
                        'prixVente' => 300,
                    ],
                    [
                        'article_id' => 6,
                        'libelle' => 'Tomate concentree (boite)',
                        'quantite' => 10,
                        'prixVente' => 450,
                    ],
                ]),
            ],
            [
                'date' => now()->subDays(5)->toDateString(),
                'montant' => 35000,
                'montantDu' => 0,
                'montantRestant' => 35000,
                'client_id' => 2,
                'status' => 'NonSolde',
                'article_details' => json_encode([
                    [
                        'article_id' => 2,
                        'libelle' => 'Huile (bidon 5L)',
                        'quantite' => 4,
                        'prixVente' => 8500,
                    ],
                    [
                        'article_id' => 10,
                        'libelle' => 'Cube Maggi (boite)',
                        'quantite' => 10,
                        'prixVente' => 300,
                    ],
                ]),
            ],
        ];

        foreach ($dettes as $dette) {
            Dette::create($dette);
        }
    }
}
