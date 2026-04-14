<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        DB::table('permissions')->insert([
            ['name' => 'create user', 'guard_name' => 'web'],
            ['name' => 'view user', 'guard_name' => 'web'],
            ['name' => 'update user', 'guard_name' => 'web'],
            ['name' => 'delete user', 'guard_name' => 'web'],
            ['name' => 'create role', 'guard_name' => 'web'],
            ['name' => 'view role', 'guard_name' => 'web'],
            ['name' => 'update role', 'guard_name' => 'web'],
            ['name' => 'delete role', 'guard_name' => 'web'],
        ]);
    }
}