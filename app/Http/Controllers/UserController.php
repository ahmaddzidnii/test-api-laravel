<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\HttpResponses;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use HttpResponses;

    public function getUserInfo(Request $request)
    {
        $users = User::paginate(10);
        return $this->successWithPagination(UserResource::collection($users->items()), $users, "Users retrieved successfully");
    }

    public function updateUserInfo(UpdateUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::find($validated['userId']);
        if (!$user) {
            return $this->error(null, 'User not found', 404);
        }

        $user->role = $validated['role'];
        $user->save();

        return $this->success(new UserResource($user), 'User updated successfully');
    }

    public function createUser(CreateUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::firstOrCreate(
            ['email' => $validated['username']],
            [
                'name'     => $validated['name'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role'     => 'ADMIN',
            ]
        );

        // Update field lain tapi JANGAN password
        $user->update([
            'name' => $validated['name'],
        ]);

        return $this->success(new UserResource($user), 'User created successfully', 201);
    }

    public function deleteUser(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return $this->error(null, 'User not found', 404);
        }

        $user->delete();

        return $this->success(null, 'User deleted successfully');
    }
}
