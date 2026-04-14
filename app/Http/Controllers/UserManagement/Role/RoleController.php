<?php

namespace App\Http\Controllers\UserManagement\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserManagement\Role\StoreRoleRequest;
use App\Http\Requests\UserManagement\Role\UpdateRoleRequest;
use App\Models\Role;

class RoleController extends Controller
{
    // Get All roles along with permissions sorted by name 
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Roles Fetched Successfully',
            'roles' => Role::with('permissions')->get()
        ], 200);
    }

    // Get a single role by id 
    public function show($id)
    {
        $role = Role::findOrFail($id); 

        return response()->json([
            'success' => true,
            'role' => $role,
        ], 200);
    }

    // User can create new role 
    public function store(StoreRoleRequest $request)
    {
        // Validate all fields for creating role 
        $role = Role::create([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
        ]);
        
        // Check if permissions are provided and sync them with the role, 
        if (!empty($request->validated('permissions'))) {
            $role->syncPermissions($request->validated('permissions'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'role' => $role->load('permissions'),
        ], 201);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validated();

        $fields = [
            'name' => $validated['name'] ?? null,
            'description' => $validated['description'] ?? null
        ];

        $role->update($fields);

        // Check if permissions are provided and sync them with the role, it doesn't throw error if permissions are not provided in the request 
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'role' => $role->load('permissions'),
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ], 200);

    }

}
