<?php

namespace App\Services;

use App\Models\Role;

class RoleService
{
    public function getAllRoles()
    {
        return Role::with('permissions')->get();
    }

    public function getRoleById($id)
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function createRole(Role $role)
    {
        $role = Role::create([
            'name' => $role->validated('name'),
            'description' => $role->validated('description'),
        ]);
        
        // Check if permissions are provided and sync them with the role, 
        if (!empty($role->validated('permissions'))) {
            $role->syncPermissions($role->validated('permissions'));
        }
        return $role->load('permissions');
    }

    public function updateRole($id, Role $role)
    {
        $role = Role::findOrFail($id);

        $role->update([
            'name' => $role->validated('name') ?? $role->name,
            'description' => $role->validated('description') ?? $role->description,
        ]);

        // Check if permissions are provided and sync them with the role, it doesn't throw error if permissions are not provided in the role 
        if (!empty($role->validated('permissions'))) {
            $role->syncPermissions($role->validated('permissions'));
        }
        return $role->load('permissions');
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        return $role->delete();
    }
}