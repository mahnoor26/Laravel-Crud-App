<?php

namespace App\Services\UserManagement;

use App\Models\User;
use App\Services\FileService;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{

    public function __construct(private readonly FileService $fileService) {}

    public function getAllUsers()
    {
        return User::with(['roles.permissions'])->get();
    }

    public function getUserById($id)
    {
        return User::with(['roles.permissions'])->findOrFail($id);
    }

    public function createUser(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign role if provided
        if (isset($data['role_id'])) {
            $role = Role::findOrFail($data['role_id']);
            $user->assignRole($role);
        }

        return $user->load(['roles.permissions']);
    }

    public function updateUser($id, array $data)
    {
        $user = User::findOrFail($id);

        // Hash password if provided, otherwise remove it to avoid overwriting
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // avoid overwriting existing password
        }

        $user->update($data);

        return $user->load(['roles.permissions']);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $this->fileService->deleteFilesForEntity('user', $user->id);
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