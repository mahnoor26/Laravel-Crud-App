<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create Admin Role
        $role = Role::create(['name' => 'admin', 'description' => 'Administrator with full permissions']);

        // Assign all permissions to admin
        $permissions = Permission::all();
        $role->syncPermissions($permissions);
    }
}