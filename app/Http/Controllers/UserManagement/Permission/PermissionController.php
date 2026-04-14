<?php

namespace App\Http\Controllers\UserManagement\Permission;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'permissions' => $permissions,
        ], 200);
    }
}
