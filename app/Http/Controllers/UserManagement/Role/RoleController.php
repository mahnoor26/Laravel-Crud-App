<?php

namespace App\Http\Controllers\UserManagement\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserManagement\Role\StoreRoleRequest;
use App\Http\Requests\UserManagement\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Services\RoleService;
use F9Web\ApiResponseHelpers;

class RoleController extends Controller
{
    use ApiResponseHelpers;

    public function __construct(private readonly RoleService $roleService) {}

    // Get All roles along with permissions sorted by name 
    public function index()
    {
        return $this->respondWithSuccess([
            'message' => 'Roles Fetched Successfully',
            'roles' => $this->roleService->getAllRoles()
        ]);
    }

    // Get a single role by id 
    public function show($id)
    {
        $role = $this->roleService->getRoleById($id);

        return $this->respondWithSuccess([
            'role' => $role,
        ]);
    }

    // User can create new role 
    public function store(StoreRoleRequest $request)
    {
        // Validate all fields for creating role 
        $role = $this->roleService->createRole($request->validated());

        return $this->respondCreated([
            'message' => 'Role created successfully',
            'role' => $role->load('permissions'),
        ]);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = $this->roleService->updateRole($request->validated(), $id);

        return $this->respondWithSuccess([
            'message' => 'Role updated successfully',
            'role' => $role->load('permissions'),
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return $this->respondWithSuccess([
            'message' => 'Role deleted successfully',
        ]);

    }

}
