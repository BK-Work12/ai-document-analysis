<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DocumentRequirementsSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'sean.cavanagh@saferwealth.com',
            'role' => 'admin',
            'password' => bcrypt('cavanagh@CA@sa'),
        ]);

        User::factory()->create([
            'name' => 'Client User',
            'email' => 'bilalkhaliddev1@gmail.com',
            'role' => 'client',
            'password' => bcrypt('Bilal@1234'),
        ]);
    }
}
