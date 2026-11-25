<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // password otomatis di-hash oleh cast 'hashed' di Model User
        User::firstOrCreate(
            ['email' => 'ega@flipplay.test'],
            ['name' => 'ega', 'password' => 'password', 'plan' => 'Free']
        );

        User::firstOrCreate(
            ['email' => 'sora@flipplay.test'],
            ['name' => 'sora', 'password' => 'password', 'plan' => 'Premium']
        );
    }
}
