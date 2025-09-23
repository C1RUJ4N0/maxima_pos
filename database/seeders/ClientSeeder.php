<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Cliente general
        Client::create(['name' => 'Cliente General']);
        // Clientes de prueba
        Client::factory(20)->create();
    }
}