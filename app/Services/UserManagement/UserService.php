<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    public function getAllUsers()
    {
        return User::with(['roles.permissions'])->get();
    }

    public function getUserById($id)
    {
        return User::with(['roles.permissions'])->findOrFail($id);
    }

    public function createUser(User $user)
    {
        $user = User::create([
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => Hash::make($user['password']),
        ]);

        // Assign role
        $role = Role::findOrFail($user['role_id']);
        $user->assignRole($role);

        return $user->load(['roles.permissions']);
    }

    public function updateUser($id, User $user)
    {
        $user = User::findOrFail($id);

        if (!empty($user['password'])) {
            $user['password'] = Hash::make($user['password']);
        } else {
            unset($user['password']); // avoid overwriting
        }

        $user->update($user);

        return $user->load(['roles.permissions']);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    public function updateStatus($id, $status)
    {
        $user = User::findOrFail($id);

        $user->update([
            'status' => $status,
        ]);

        return $user;
    }
}