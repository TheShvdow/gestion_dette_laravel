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
        Dette::factory()->count(10)->create();

    }
}
