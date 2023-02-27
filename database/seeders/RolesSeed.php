<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin'
            ],
            [
                'name' => 'employee'
            ],
            [
                'name' => 'user'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
