<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@arkdev.com',
            'password' => Hash::make('supersecurepassword'),
            'adminArk' => 'adminArk',
            'estado' => 1,
            'ci' => '12345678',
            'telefono' => '77777777',
            'email_verified_at' => now(),
        ]);
    }
}
