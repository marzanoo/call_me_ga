<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TechnicianUserSeeder extends Seeder
{
    /**
     * Seed the application's technician account.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'teknisi'],
            [
                'nik' => 'TEKNISI001',
                'name' => 'Teknisi GA',
                'email' => 'teknisi@callmega.local',
                'role' => config('callmega.roles.technician'),
                'email_verified_at' => now(),
                'password' => Hash::make(env('TECHNICIAN_PASSWORD', 'password')),
            ]
        );
    }
}
