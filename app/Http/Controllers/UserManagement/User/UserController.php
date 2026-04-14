<?php

namespace App\Http\Controllers\UserManagement\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserManagement\User\StoreUserRequest as UserStoreUserRequest;
use App\Http\Requests\UserManagement\User\UpdateUserRequest as UserUpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET API /users
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'User Fetched Successfully',
            'users' => User::with(['roles.permissions'])->get(),
        ], 200);
    }

    // POST API /users
    public function store(UserStoreUserRequest $request)
    {
        $fields = $request->validated();

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        // Assign role
        $role = \Spatie\Permission\Models\Role::findOrFail($fields['role_id']);
        $user->assignRole($role);

        $user->load(['roles.permissions']);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    // GET /users/{id}
    public function show($id)
    {
        $user = User::with(['roles.permissions'])->findOrFail($id); 

        return response()->json([
            'success' => true,
            'user' => $user,
        ], 200);
    }

    // PUT API /users/{id}
    public function update(UserUpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $fields = $request->validated();

        if (isset($fields['password'])) {
            $fields['password'] = Hash::make($fields['password']);
        }

        $user->update([
            'name' => $fields['name'] ?? $user->name,
            'email' => $fields['email'] ?? $user->email,
            'password' => $fields['password'] ?? $user->password,
        ]);

        $user->load(['roles.permissions']);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user,
        ], 200);
    }

    // DELETE API /users/{id}
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ], 200);
    }

    public function updateStatus(Request $request , $id)
    {
        $user = User::findOrFail($id);
        $user->status = $request->status;
        $user->update([
                'status' => $request->status,
            ]);
            
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
        ], 200);
    }
}
