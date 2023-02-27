<?php

namespace Database\Seeders;


use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            RolesSeed::class
        ]);
        User::create(
            [
                'name'=> 'admin',
                'email'=> 'admin@gmail.com',
                'password'=> bcrypt('nimda'),
                'role_id'=> 1,
                'phone'=> '8139895086',
                'active' => 1
            ]
        );
        User::create(
            [
                'name'=> 'employee',
                'email'=> 'employee@gmail.com',
                'password'=> bcrypt('employee'),
                'role_id'=> 2,
                'phone'=> '8139895086',
                'active' => 1
            ]
        );

    }
}
