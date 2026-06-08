<?php

namespace App\Http\Controllers\UserManagement\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserManagement\User\StoreUserRequest;
use App\Http\Requests\UserManagement\User\UpdateUserRequest;
use App\Services\UserManagement\UserService;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseHelpers;
    public function __construct(private readonly UserService $userService) {}

    // GET API /users
    public function index()
    {
        return $this->respondWithSuccess([
            'message' => 'User Fetched Successfully',
            'users' => $this->userService->getAllUsers(),
        ]);
    }

    // POST API /users
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());     

        return $this->respondCreated([
            'message' => 'User created successfully',
            'user' => $user,
        ]);
    }

    // GET /users/{id}
    public function show($id)
    {
        $user = $this->userService->getUserById($id);

        return $this->respondWithSuccess([
             'message' => 'User Fetched Successfully',
            'user' => $user,
        ]);
    }

    // PUT API /users/{id}
    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userService->updateUser($id, $request->validated());

        return $this->respondWithSuccess([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    // DELETE API /users/{id}
    public function destroy($id)
    {
        $this->userService->deleteUser($id);

        return $this->respondWithSuccess([
            'message' => 'User deleted successfully',
        ]);
    }

    public function updateStatus(Request $request , $id)
    {
        $this->userService->updateStatus($id, $request->status);
            
        return $this->respondWithSuccess([
            'message' => 'Status updated successfully',
        ]);
    }
}
