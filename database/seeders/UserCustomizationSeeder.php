<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserCustomization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserCustomizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear personalizaciones para usuarios existentes
        $users = User::all();
        
        foreach ($users as $user) {
            // Solo crear si no existe una personalización
            if (!$user->customization) {
                UserCustomization::create([
                    'user_id' => $user->id,
                    ...UserCustomization::getDefaults()
                ]);
            }
        }
    }
}
