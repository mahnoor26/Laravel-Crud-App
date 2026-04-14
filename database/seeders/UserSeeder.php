<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Mahnoor Munir',
            'email' => 'mahnoormunir901@gmail.com',
            'password' => bcrypt('password'),
            'status' => 'active',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        // Assign Admin Role
        $user->assignRole('admin');
     }
}