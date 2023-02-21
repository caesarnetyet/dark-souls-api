<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Role::create([
            'name' => 'admin',
            ]);
        Role::create([
            'name' => 'user',
            ]);
        Role::create([
            'name' => 'guest',
            ]);
    }
}
