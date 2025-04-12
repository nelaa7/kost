<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\listing;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin Warsu',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('Admin123#'),
        ]);


        $listings = Listing::factory(10)->create();
    }
}
