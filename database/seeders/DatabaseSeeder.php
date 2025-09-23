<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear un administrador
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@maxima.com',
        ]);

        // Crear usuarios de prueba
        User::factory(10)->create();

        // Llamar a otros seeders
        $this->call([
            ProductSeeder::class,
            ClientSeeder::class,
        ]);
    }
}
