<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'Administrador', 'slug' => 'admin'],
            ['id' => 2, 'name' => 'Entrenador', 'slug' => 'coach'],
            ['id' => 3, 'name' => 'Jugador', 'slug' => 'player'],
            ['id' => 4, 'name' => 'Árbitro', 'slug' => 'referee']
        ];
    
        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
    }
}
