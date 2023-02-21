<?php

namespace Database\Seeders;

use App\Models\heroes;
use Illuminate\Database\Seeder;

class heroesSeed extends Seeder
{

    public function run(): void
    {
        Heroes::insert([
            [ 'name' => 'Dr. Nice'],
            [ 'name' => 'Narco'],
            [ 'name' => 'Bombasto'],
            [ 'name' => 'Celeritas'],
            [ 'name' => 'Magneta'],
            [ 'name' => 'RubberMan'],
            [ 'name' => 'Dynama'],
            [ 'name' => 'Dr IQ'],
            [ 'name' => 'Magma'],
            [ 'name' => 'Tornado']
        ]);
    }
}
