<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\UserDetail;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with(['userDetail', 'role'])->get();

            return response()->json([
                'message' => 'Users retrieved successfully',
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $user = User::createUser($validated);

            UserDetail::createUserDetail($user->id, $validated);

            DB::commit();

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $user = User::with(['userDetail', 'role'])->find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            return response()->json([
                'message' => 'User retrieved successfully',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            $user = User::with(['userDetail'])->find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $validated = $request->validated();
            $user->updateUser($validated);

            if ($user->userDetail) {
                $user->userDetail->updateUserDetail($validated);
            } else {
                UserDetail::createUserDetail($user->id, $validated);
            }

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::with(['userDetail'])->find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getGeneralData()
    {
        try {
            $roles = Role::ordered()->get();

            return response()->json([
                'message' => 'General data retrieved successfully',
                'roles' => $roles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving general data',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'General data retrieved successfully',
            'roles' => $roles,
        ]);
    }
}
