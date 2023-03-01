<?php

namespace Database\Seeders;

use App\Models\Classe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class classSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Classe::create([
            'name' => 'Warrior',
        ]);
        Classe::create([
            'name' => 'Mage',
        ]);
        Classe::create([
            'name' => 'Alquemist',
        ]);

    }
}
